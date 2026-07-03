<?php

namespace App\Services;

use App\Mail\TalentApprovedMail;
use App\Mail\TalentRejectedMail;
use App\Models\ModerationRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class UserModerationService
{
    public function __construct(private UserDeletionService $userDeletion) {}
    public function submit(User $actor, string $action, ?User $target = null, array $payload = []): ModerationRequest|string
    {
        $this->guardAction($actor, $action, $target);

        if ($actor->isAdmin()) {
            $this->execute($action, $target, $payload, $actor);

            return 'executed';
        }

        return ModerationRequest::create([
            'requested_by' => $actor->id,
            'action_type' => $action,
            'target_user_id' => $target?->id,
            'payload' => $payload ?: null,
            'status' => ModerationRequest::STATUS_PENDING,
        ]);
    }

    public function approveRequest(ModerationRequest $request, User $admin, ?string $note = null): void
    {
        if (! $admin->isAdmin()) {
            abort(403);
        }

        if (! $request->isPending()) {
            throw ValidationException::withMessages([
                'request' => __('talenma.admin.users.request_already_processed'),
            ]);
        }

        DB::transaction(function () use ($request, $admin, $note) {
            $target = $request->targetUser;

            $this->execute(
                $request->action_type,
                $target,
                $request->payload ?? [],
                $admin,
            );

            $request->update([
                'status' => ModerationRequest::STATUS_APPROVED,
                'reviewed_by' => $admin->id,
                'reviewed_at' => now(),
                'admin_note' => $note,
            ]);
        });
    }

    public function rejectRequest(ModerationRequest $request, User $admin, ?string $note = null): void
    {
        if (! $admin->isAdmin()) {
            abort(403);
        }

        if (! $request->isPending()) {
            throw ValidationException::withMessages([
                'request' => __('talenma.admin.users.request_already_processed'),
            ]);
        }

        $request->update([
            'status' => ModerationRequest::STATUS_REJECTED,
            'reviewed_by' => $admin->id,
            'reviewed_at' => now(),
            'admin_note' => $note,
        ]);
    }

    private function guardAction(User $actor, string $action, ?User $target): void
    {
        if (! $actor->isStaff()) {
            abort(403);
        }

        if (in_array($action, [
            ModerationRequest::ACTION_GRANT_MODERATOR,
            ModerationRequest::ACTION_REVOKE_MODERATOR,
        ], true) && ! $actor->isAdmin()) {
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
            ModerationRequest::ACTION_APPROVE_TALENT => $this->approveTalent($target, $actor),
            ModerationRequest::ACTION_REJECT_TALENT => $this->rejectTalent($target, $payload['reason'] ?? null, $actor),
            ModerationRequest::ACTION_DELETE_USER => $this->deleteUser($target),
            ModerationRequest::ACTION_CREATE_USER => $this->createUser($payload, $actor),
            ModerationRequest::ACTION_GRANT_MODERATOR => $this->grantModerator($target),
            ModerationRequest::ACTION_REVOKE_MODERATOR => $this->revokeModerator($target),
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
                'title' => null,
                'bio' => null,
                'experience_years' => 0,
                'country' => 'Maroc',
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

    public function deleteUser(User $user): void
    {
        $this->userDeletion->delete($user);
    }

    public function createUser(array $payload, User $actor): User
    {
        $role = $payload['role'] ?? 'dev';

        if (! in_array($role, ['dev', 'company'], true)) {
            throw ValidationException::withMessages([
                'role' => __('talenma.admin.users.invalid_role'),
            ]);
        }

        $user = User::create([
            'name' => $payload['name'],
            'email' => $payload['email'],
            'password' => $payload['password'],
            'role' => $role,
            'email_verified_at' => ($payload['email_verified'] ?? false) ? now() : null,
            'approval_status' => $role === 'dev'
                ? (($payload['approve_immediately'] ?? false) ? 'approved' : 'pending')
                : 'approved',
            'approved_at' => ($role === 'dev' && ($payload['approve_immediately'] ?? false)) ? now() : null,
            'approved_by' => ($role === 'dev' && ($payload['approve_immediately'] ?? false)) ? $actor->id : null,
        ]);

        if ($role === 'dev' && $user->approval_status === 'approved') {
            $user->profile()->create([
                'title' => null,
                'bio' => null,
                'experience_years' => 0,
                'country' => 'Maroc',
            ]);
        }

        if ($role === 'company') {
            $user->companyProfile()->create([
                'country' => $payload['country'] ?? 'France',
            ]);
        }

        return $user;
    }

    public function grantModerator(User $user): void
    {
        if ($user->isAdmin()) {
            throw ValidationException::withMessages([
                'user' => __('talenma.admin.users.already_admin'),
            ]);
        }

        $user->update(['role' => 'moderator']);
    }

    public function revokeModerator(User $user): void
    {
        if (! $user->isModerator()) {
            throw ValidationException::withMessages([
                'user' => __('talenma.admin.users.not_a_moderator'),
            ]);
        }

        $user->update(['role' => 'dev', 'approval_status' => 'approved']);
    }
}
