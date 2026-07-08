<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreManagedUserRequest;
use App\Models\ModerationRequest;
use App\Models\User;
use App\Services\TalentDossierPresenter;
use App\Services\UserModerationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function __construct(private UserModerationService $moderation)
    {
    }

    public function index(Request $request): View
    {
        $filter = $request->string('filter')->toString() ?: 'pending';

        $usersQuery = User::query()
            ->with('approvedBy')
            ->whereIn('role', ['dev', 'company', 'moderator'])
            ->latest();

        if ($filter === 'pending') {
            $usersQuery
                ->whereIn('role', ['dev', 'company'])
                ->where('approval_status', User::APPROVAL_PENDING)
                ->whereNotNull('email_verified_at')
                ->with([
                    'profile.professionSector',
                    'profile.documents',
                    'companyProfile.documents',
                ]);
        } elseif ($filter === 'talents') {
            $usersQuery
                ->where('role', 'dev')
                ->with(['profile.professionSector', 'profile.profession', 'profile.documents', 'approvedBy']);
        } elseif ($filter === 'companies') {
            $usersQuery->where('role', 'company');
        } elseif ($filter === 'moderators') {
            $usersQuery->where('role', 'moderator');
        } else {
            $usersQuery->with(['profile.professionSector', 'profile.profession', 'profile.documents', 'approvedBy']);
        }

        $pendingRequests = $request->user()->isAdmin()
            ? ModerationRequest::query()
                ->with(['requester', 'targetUser'])
                ->where('status', ModerationRequest::STATUS_PENDING)
                ->latest()
                ->get()
            : collect();

        return view('admin.users.index', [
            'users' => $usersQuery->paginate(20)->withQueryString(),
            'filter' => $filter,
            'pendingRequests' => $pendingRequests,
            'pendingCount' => User::query()
                ->whereIn('role', ['dev', 'company'])
                ->where('approval_status', User::APPROVAL_PENDING)
                ->whereNotNull('email_verified_at')
                ->count(),
        ]);
    }

    public function registration(User $user, TalentDossierPresenter $presenter): JsonResponse
    {
        abort_unless(($user->isTalent() || $user->isCompany()) && $user->hasVerifiedEmail(), 404);

        return response()->json($presenter->present($user));
    }

    public function store(StoreManagedUserRequest $request): RedirectResponse
    {
        $payload = $request->validatedPayload();

        $result = $this->moderation->submit(
            $request->user(),
            ModerationRequest::ACTION_CREATE_USER,
            null,
            $payload,
        );

        $message = $result === 'executed'
            ? 'user_created'
            : 'request_submitted';

        return back()->with($message, true);
    }

    public function approve(Request $request, User $user): RedirectResponse
    {
        $action = $user->isCompany()
            ? ModerationRequest::ACTION_APPROVE_COMPANY
            : ModerationRequest::ACTION_APPROVE_TALENT;

        $result = $this->moderation->submit(
            $request->user(),
            $action,
            $user,
        );

        return back()->with($result === 'executed' ? 'user_approved' : 'request_submitted', true);
    }

    public function reject(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'reason' => ['nullable', 'string', 'max:1000'],
        ]);

        $action = $user->isCompany()
            ? ModerationRequest::ACTION_REJECT_COMPANY
            : ModerationRequest::ACTION_REJECT_TALENT;

        $result = $this->moderation->submit(
            $request->user(),
            $action,
            $user,
            ['reason' => $request->string('reason')->toString() ?: null],
        );

        return back()->with($result === 'executed' ? 'user_rejected' : 'request_submitted', true);
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        $result = $this->moderation->submit(
            $request->user(),
            ModerationRequest::ACTION_DELETE_USER,
            $user,
        );

        return back()->with($result === 'executed' ? 'user_deleted' : 'request_submitted', true);
    }

    public function grantModerator(Request $request, User $user): RedirectResponse
    {
        $this->moderation->submit(
            $request->user(),
            ModerationRequest::ACTION_GRANT_MODERATOR,
            $user,
        );

        return back()->with('moderator_granted', true);
    }

    public function revokeModerator(Request $request, User $user): RedirectResponse
    {
        $this->moderation->submit(
            $request->user(),
            ModerationRequest::ACTION_REVOKE_MODERATOR,
            $user,
        );

        return back()->with('moderator_revoked', true);
    }

    public function approveRequest(Request $request, ModerationRequest $moderationRequest): RedirectResponse
    {
        $this->moderation->approveRequest(
            $moderationRequest,
            $request->user(),
            $request->string('admin_note')->toString() ?: null,
        );

        return back()->with('request_approved', true);
    }

    public function rejectRequest(Request $request, ModerationRequest $moderationRequest): RedirectResponse
    {
        $this->moderation->rejectRequest(
            $moderationRequest,
            $request->user(),
            $request->string('admin_note')->toString() ?: null,
        );

        return back()->with('request_rejected', true);
    }
}
