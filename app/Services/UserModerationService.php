<?php

namespace App\Services;

use App\Mail\CompanyApprovedMail;
use App\Mail\CompanyRejectedMail;
use App\Mail\TalentApprovedMail;
use App\Mail\TalentRejectedMail;
use App\Models\ModerationAction;
use App\Models\ModeratorPermissionCatalog;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class UserModerationService
{
    public function __construct(
        private UserDeletionService $userDeletion,
        private ModeratorAssignmentService $moderatorAssignments,
    ) {}

    public function submit(User $actor, string $action, ?User $target = null, array $payload = []): void
    {
        $this->guardAction($actor, $action, $target);
        $this->execute($action, $target, $payload, $actor);
    }

    private function guardAction(User $actor, string $action, ?User $target): void
    {
        if (! $actor->isStaff()) {
            abort(403);
        }

        $requiredPermission = match ($action) {
            ModerationAction::CREATE_USER => null,
            ModerationAction::APPROVE_TALENT,
            ModerationAction::APPROVE_COMPANY => ModeratorPermissionCatalog::ACCOUNTS_APPROVE,
            ModerationAction::REJECT_TALENT,
            ModerationAction::REJECT_COMPANY => ModeratorPermissionCatalog::ACCOUNTS_REJECT,
            ModerationAction::DELETE_USER => ModeratorPermissionCatalog::ACCOUNTS_DELETE,
            ModerationAction::GRANT_MODERATOR,
            ModerationAction::REVOKE_MODERATOR => null,
            default => null,
        };

        if (in_array($action, [
            ModerationAction::CREATE_USER,
            ModerationAction::GRANT_MODERATOR,
            ModerationAction::REVOKE_MODERATOR,
        ], true) && ! $actor->isAdmin()) {
            abort(403);
        }

        if ($requiredPermission !== null && ! $actor->hasModeratorPermission($requiredPermission)) {
            abort(403);
        }

        if (
            $action === ModerationAction::DELETE_USER
            && $target?->isModerator()
            && ! $actor->isAdmin()
        ) {
            abort(403);
        }

        if ($target && $target->isAdmin()) {
            throw ValidationException::withMessages([
                'user' => __('talenma.admin.users.cannot_modify_admin'),
            ]);
        }

        if ($target && $actor->id === $target->id) {
            throw ValidationException::withMessages([
                'user' => __('talenma.admin.users.cannot_modify_self'),
            ]);
        }
    }

    private function execute(string $action, ?User $target, array $payload, User $actor): void
    {
        match ($action) {
            ModerationAction::APPROVE_TALENT => $this->approveTalent($target, $actor),
            ModerationAction::REJECT_TALENT => $this->rejectTalent($target, $payload['reason'] ?? null, $actor),
            ModerationAction::APPROVE_COMPANY => $this->approveCompany($target, $actor),
            ModerationAction::REJECT_COMPANY => $this->rejectCompany($target, $payload['reason'] ?? null, $actor),
            ModerationAction::DELETE_USER => $this->deleteUser($target, $actor),
            ModerationAction::CREATE_USER => $this->createUser($payload, $actor),
            ModerationAction::GRANT_MODERATOR => $this->grantModerator($actor, $target, $payload['permissions'] ?? []),
            ModerationAction::REVOKE_MODERATOR => $this->revokeModerator($actor, $target),
            default => throw ValidationException::withMessages([
                'action' => __('talenma.admin.users.unknown_action'),
            ]),
        };
    }

    public function approveTalent(User $user, User $approver): void
    {
        if (! $user->isTalent()) {
            throw ValidationException::withMessages([
                'user' => __('talenma.admin.users.not_a_talent'),
            ]);
        }

        $user->update([
            'approval_status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $approver->id,
            'rejection_reason' => null,
        ]);

        if (! $user->profile) {
            $user->profile()->create([
                'bio' => null,
                'experience_years' => 0,
            ]);
        }

        Mail::to($user->email)->send(new TalentApprovedMail($user->fresh()));
    }

    public function rejectTalent(User $user, ?string $reason, User $reviewer): void
    {
        if (! $user->isTalent()) {
            throw ValidationException::withMessages([
                'user' => __('talenma.admin.users.not_a_talent'),
            ]);
        }

        $user->update([
            'approval_status' => 'rejected',
            'approved_at' => null,
            'approved_by' => $reviewer->id,
            'rejection_reason' => $reason,
        ]);

        Mail::to($user->email)->send(new TalentRejectedMail($user->fresh(), $reason));
    }

    public function approveCompany(User $user, User $approver): void
    {
        if (! $user->isCompany()) {
            throw ValidationException::withMessages([
                'user' => __('talenma.admin.users.not_a_company'),
            ]);
        }

        $user->update([
            'approval_status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $approver->id,
            'rejection_reason' => null,
        ]);

        if (! $user->companyProfile) {
            $user->companyProfile()->create();
        }

        Mail::to($user->email)->send(new CompanyApprovedMail($user->fresh()->loadMissing('companyProfile')));
    }

    public function rejectCompany(User $user, ?string $reason, User $reviewer): void
    {
        if (! $user->isCompany()) {
            throw ValidationException::withMessages([
                'user' => __('talenma.admin.users.not_a_company'),
            ]);
        }

        $user->update([
            'approval_status' => 'rejected',
            'approved_at' => null,
            'approved_by' => $reviewer->id,
            'rejection_reason' => $reason,
        ]);

        Mail::to($user->email)->send(new CompanyRejectedMail($user->fresh(), $reason));
    }

    public function deleteUser(User $user, User $actor): void
    {
        $this->userDeletion->delete($user, $actor);
    }

    public function createUser(array $payload, User $actor): User
    {
        if (! $actor->isAdmin()) {
            abort(403);
        }

        $role = $payload['role'] ?? 'dev';

        if (! in_array($role, ['dev', 'company'], true)) {
            throw ValidationException::withMessages([
                'role' => __('talenma.admin.users.invalid_role'),
            ]);
        }

        $firstName = isset($payload['first_name']) ? trim((string) $payload['first_name']) : null;
        $lastName = isset($payload['last_name']) ? trim((string) $payload['last_name']) : null;
        $name = trim((string) ($payload['name'] ?? ''));

        if ($name === '' && ($firstName !== null || $lastName !== null)) {
            $name = trim(($firstName ?? '').' '.($lastName ?? ''));
        }

        $user = User::create([
            'name' => $name,
            'first_name' => filled($firstName) ? $firstName : null,
            'last_name' => filled($lastName) ? $lastName : null,
            'email' => $payload['email'],
            'password' => $payload['password'],
            'role' => $role,
            'email_verified_at' => ($payload['email_verified'] ?? false) ? now() : null,
            'approval_status' => ($payload['approve_immediately'] ?? false)
                ? 'approved'
                : 'pending',
            'approved_at' => ($payload['approve_immediately'] ?? false) ? now() : null,
            'approved_by' => ($payload['approve_immediately'] ?? false) ? $actor->id : null,
        ]);

        if ($role === 'dev' && $user->approval_status === 'approved') {
            $user->profile()->create([
                'bio' => null,
                'experience_years' => 0,
            ]);
        }

        if ($role === 'company') {
            $user->update(['company_seat' => User::SEAT_OWNER]);
            $user->companyProfile()->create([
                'representative_name' => $payload['representative_name']
                    ?? trim(($firstName ?? '').' '.($lastName ?? '')),
                'country' => filled($payload['country'] ?? null) ? $payload['country'] : null,
            ]);
        }

        return $user;
    }

    /**
     * @param  list<string>  $permissions
     */
    public function grantModerator(User $admin, User $user, array $permissions = []): void
    {
        $this->moderatorAssignments->grant($admin, $user, $permissions);
    }

    /**
     * @return array{recovered_direct_hires: int, recovered_direct_hire_ids: list<int>}
     */
    public function revokeModerator(User $admin, User $user): array
    {
        $recovery = $this->moderatorAssignments->revoke($admin, $user);

        if ($admin->is($user) || (auth()->id() === $user->id)) {
            $this->moderatorAssignments->clearActingMode();
        }

        DB::table('sessions')->where('user_id', $user->id)->delete();

        return $recovery;
    }
}
