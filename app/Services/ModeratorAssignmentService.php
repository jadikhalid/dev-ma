<?php

namespace App\Services;

use App\Models\DirectHireRequest;
use App\Models\ModeratorAssignment;
use App\Models\ModeratorAuditLog;
use App\Models\ModeratorPermission;
use App\Models\ModeratorPermissionCatalog;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ModeratorAssignmentService
{
    public const SESSION_MODE_KEY = 'acting_as_moderator';

    /**
     * @param  list<string>  $permissions
     */
    public function grant(User $admin, User $talent, array $permissions = []): ModeratorAssignment
    {
        if (! $admin->isAdmin()) {
            abort(403);
        }

        if (! $talent->isTalent() || ! $talent->isApproved()) {
            throw ValidationException::withMessages([
                'user' => __('talenma.admin.users.moderator_must_be_approved_talent'),
            ]);
        }

        if ($talent->isAdmin()) {
            throw ValidationException::withMessages([
                'user' => __('talenma.admin.users.cannot_modify_admin'),
            ]);
        }

        $permissions = $this->normalizePermissions($permissions);

        return DB::transaction(function () use ($admin, $talent, $permissions) {
            $assignment = ModeratorAssignment::query()
                ->where('user_id', $talent->id)
                ->first();

            if ($assignment) {
                $assignment->update([
                    'granted_by' => $admin->id,
                    'granted_at' => now(),
                    'revoked_by' => null,
                    'revoked_at' => null,
                ]);
            } else {
                $assignment = ModeratorAssignment::query()->create([
                    'user_id' => $talent->id,
                    'granted_by' => $admin->id,
                    'granted_at' => now(),
                ]);
            }

            $this->syncPermissions($assignment, $permissions);
            $assignment = $assignment->fresh(['permissions']);
            $this->recordAudit('granted', $talent, $assignment, $admin);

            return $assignment;
        });
    }

    /**
     * @param  list<string>  $permissions
     */
    public function syncPermissions(ModeratorAssignment $assignment, array $permissions): ModeratorAssignment
    {
        $permissions = $this->normalizePermissions($permissions);

        DB::transaction(function () use ($assignment, $permissions) {
            ModeratorPermission::query()
                ->where('moderator_assignment_id', $assignment->id)
                ->delete();

            foreach ($permissions as $permission) {
                ModeratorPermission::query()->create([
                    'moderator_assignment_id' => $assignment->id,
                    'permission' => $permission,
                ]);
            }
        });

        return $assignment->fresh(['permissions']);
    }

    /**
     * @param  list<string>  $permissions
     */
    public function updatePermissions(User $admin, User $talent, array $permissions): ModeratorAssignment
    {
        if (! $admin->isAdmin()) {
            abort(403);
        }

        return DB::transaction(function () use ($admin, $talent, $permissions) {
            $assignment = $this->lockedActiveAssignment($talent);
            $oldPermissions = $assignment->permissionKeys();
            $assignment = $this->syncPermissions($assignment, $permissions);

            $this->recordAudit('permissions_updated', $talent, $assignment, $admin, [
                'previous_permissions' => $oldPermissions,
            ]);

            return $assignment;
        });
    }

    /**
     * @return array{recovered_direct_hires: int, recovered_direct_hire_ids: list<int>}
     */
    public function revoke(User $admin, User $talent): array
    {
        if (! $admin->isAdmin()) {
            abort(403);
        }

        return DB::transaction(function () use ($admin, $talent) {
            $assignment = $this->lockedActiveAssignment($talent);
            $recovery = $this->recoverOpenWork($talent, $admin);

            $this->recordAudit('revoked', $talent, $assignment, $admin, $recovery);

            $assignment->update([
                'revoked_by' => $admin->id,
                'revoked_at' => now(),
            ]);

            return $recovery;
        });
    }

    /**
     * Revoke an active assignment as part of deleting the Talent account.
     *
     * @return array{recovered_direct_hires: int, recovered_direct_hire_ids: list<int>}
     */
    public function revokeForDeletion(User $admin, User $talent): array
    {
        if (! $admin->isAdmin()) {
            abort(403);
        }

        return DB::transaction(function () use ($admin, $talent) {
            $assignment = $this->lockedActiveAssignment($talent);
            $recovery = $this->recoverOpenWork($talent, $admin);

            $this->recordAudit('account_deleted', $talent, $assignment, $admin, $recovery);

            $assignment->update([
                'revoked_by' => $admin->id,
                'revoked_at' => now(),
            ]);

            return $recovery;
        });
    }

    public function setActingMode(User $user, bool $asModerator): void
    {
        if ($asModerator) {
            if (! $user->canActAsModerator()) {
                session()->forget(self::SESSION_MODE_KEY);

                throw ValidationException::withMessages([
                    'mode' => __('talenma.admin.users.moderator_mode_unavailable'),
                ]);
            }

            session([self::SESSION_MODE_KEY => true]);

            return;
        }

        session()->forget(self::SESSION_MODE_KEY);
    }

    public function clearActingMode(): void
    {
        session()->forget(self::SESSION_MODE_KEY);
    }

    /**
     * @param  list<string>|mixed  $permissions
     * @return list<string>
     */
    public function normalizePermissions(mixed $permissions): array
    {
        if (! is_array($permissions)) {
            return [];
        }

        $normalized = [];

        foreach ($permissions as $permission) {
            if (! is_string($permission) || ! ModeratorPermissionCatalog::isValid($permission)) {
                continue;
            }

            $normalized[$permission] = $permission;
        }

        return array_values($normalized);
    }

    private function lockedActiveAssignment(User $talent): ModeratorAssignment
    {
        $assignment = ModeratorAssignment::query()
            ->with('permissions')
            ->where('user_id', $talent->id)
            ->whereNull('revoked_at')
            ->lockForUpdate()
            ->first();

        if (! $assignment) {
            throw ValidationException::withMessages([
                'user' => __('talenma.admin.users.not_a_moderator'),
            ]);
        }

        return $assignment;
    }

    /**
     * Transfer only operational ownership. Historical actor fields remain unchanged.
     *
     * @return array{recovered_direct_hires: int, recovered_direct_hire_ids: list<int>}
     */
    private function recoverOpenWork(User $talent, User $admin): array
    {
        $directHireIds = DirectHireRequest::query()
            ->where('initiated_by_user_id', $talent->id)
            ->whereIn('hire_origin', DirectHireRequest::staffHireOrigins())
            ->whereIn('status', DirectHireRequest::openStatuses())
            ->lockForUpdate()
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();

        if ($directHireIds !== []) {
            DirectHireRequest::query()
                ->whereIn('id', $directHireIds)
                ->update([
                    'initiated_by_user_id' => $admin->id,
                    'staff_seen_at' => null,
                ]);
        }

        return [
            'recovered_direct_hires' => count($directHireIds),
            'recovered_direct_hire_ids' => $directHireIds,
        ];
    }

    /**
     * @param  array<string, mixed>  $context
     */
    private function recordAudit(
        string $action,
        User $talent,
        ModeratorAssignment $assignment,
        User $admin,
        array $context = [],
    ): void {
        ModeratorAuditLog::query()->create([
            'moderator_user_id' => $talent->id,
            'moderator_assignment_id' => $assignment->id,
            'moderator_name_snapshot' => $talent->formalDisplayName(),
            'moderator_email_snapshot' => $talent->email,
            'action' => $action,
            'permissions_snapshot' => $assignment->permissionKeys(),
            'context' => $context ?: null,
            'performed_by' => $admin->id,
        ]);
    }
}
