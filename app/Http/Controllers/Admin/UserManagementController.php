<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreManagedUserRequest;
use App\Models\ModerationRequest;
use App\Models\ModeratorPermissionCatalog;
use App\Models\PendingRegistration;
use App\Models\User;
use App\Services\ModeratorAssignmentService;
use App\Services\TalentDossierPresenter;
use App\Services\UserModerationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function __construct(
        private UserModerationService $moderation,
        private ModeratorAssignmentService $moderatorAssignments,
    ) {
    }

    public function index(Request $request): View
    {
        $filter = $request->string('filter')->toString() ?: 'pending';
        $search = trim($request->string('q')->toString());

        $usersQuery = User::query()
            ->with(['approvedBy', 'moderatorAssignments.permissions'])
            ->whereIn('role', ['dev', 'company'])
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
            $usersQuery
                ->where('role', 'dev')
                ->whereHas('moderatorAssignments', fn ($query) => $query->whereNull('revoked_at'))
                ->with(['profile.professionSector', 'profile.profession', 'moderatorAssignments.permissions', 'approvedBy']);
        } else {
            $usersQuery->with(['profile.professionSector', 'profile.profession', 'profile.documents', 'approvedBy']);
        }

        if ($search !== '') {
            $like = '%'.$search.'%';

            $usersQuery->where(function ($query) use ($like) {
                $query->where('name', 'like', $like)
                    ->orWhere('first_name', 'like', $like)
                    ->orWhere('last_name', 'like', $like)
                    ->orWhere('email', 'like', $like)
                    ->orWhereHas('companyProfile', function ($companyQuery) use ($like) {
                        $companyQuery->where('representative_name', 'like', $like);
                    });
            });
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
            'search' => $search,
            'pendingRequests' => $pendingRequests,
            'pendingCount' => User::query()
                ->whereIn('role', ['dev', 'company'])
                ->where('approval_status', User::APPROVAL_PENDING)
                ->whereNotNull('email_verified_at')
                ->count(),
            'canCreateAccounts' => $request->user()->isAdmin(),
            'canApproveAccounts' => $request->user()->hasModeratorPermission(ModeratorPermissionCatalog::ACCOUNTS_APPROVE),
            'canRejectAccounts' => $request->user()->hasModeratorPermission(ModeratorPermissionCatalog::ACCOUNTS_REJECT),
            'canDeleteAccounts' => $request->user()->hasModeratorPermission(ModeratorPermissionCatalog::ACCOUNTS_DELETE),
        ]);
    }

    public function registration(User $user, TalentDossierPresenter $presenter): JsonResponse
    {
        abort_unless(($user->isTalent() || $user->isCompany()) && $user->hasVerifiedEmail(), 404);

        return response()->json($presenter->present($user));
    }

    public function checkEmail(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class, 'email'),
                Rule::unique(User::class, 'pending_email'),
                Rule::unique(PendingRegistration::class, 'email'),
            ],
        ], [
            'email.required' => __('talenma.auth.validation.email_required'),
            'email.email' => __('talenma.auth.validation.email_invalid'),
            'email.unique' => __('talenma.auth.validation.email_taken'),
        ], [
            'email' => __('talenma.auth.email'),
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->get('email');

            return response()->json([
                'available' => false,
                'message' => $messages[0] ?? __('talenma.auth.validation.email_taken'),
            ], 422);
        }

        return response()->json([
            'available' => true,
            'message' => __('talenma.admin.users.email_available'),
        ]);
    }

    public function store(StoreManagedUserRequest $request): RedirectResponse|JsonResponse
    {
        $payload = $request->validatedPayload();

        $result = $this->moderation->submit(
            $request->user(),
            ModerationRequest::ACTION_CREATE_USER,
            null,
            $payload,
        );

        $messageKey = $result === 'executed'
            ? 'user_created'
            : 'request_submitted';
        $message = __('talenma.admin.users.flash.'.$messageKey);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
            ]);
        }

        return back()->with($messageKey, true);
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

    public function destroy(Request $request, User $user): RedirectResponse|JsonResponse
    {
        $result = $this->moderation->submit(
            $request->user(),
            ModerationRequest::ACTION_DELETE_USER,
            $user,
        );

        $messageKey = $result === 'executed' ? 'user_deleted' : 'request_submitted';

        if ($request->expectsJson()) {
            return response()->json([
                'message' => __('talenma.admin.users.flash.'.$messageKey),
            ]);
        }

        return back()->with($messageKey, true);
    }

    public function grantModerator(Request $request, User $user): RedirectResponse|JsonResponse
    {
        $permissions = $this->moderatorAssignments->normalizePermissions(
            $request->input('permissions', [])
        );

        $this->moderation->grantModerator($request->user(), $user, $permissions);

        $message = __('talenma.admin.users.flash.moderator_granted');

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'moderator' => $this->moderatorPayload($user->fresh(['moderatorAssignments.permissions'])),
            ]);
        }

        return back()->with('moderator_granted', true);
    }

    public function updateModeratorPermissions(Request $request, User $user): JsonResponse
    {
        $permissions = $this->moderatorAssignments->normalizePermissions(
            $request->input('permissions', [])
        );

        $assignment = $this->moderatorAssignments->updatePermissions(
            $request->user(),
            $user,
            $permissions,
        );

        return response()->json([
            'message' => __('talenma.admin.users.flash.moderator_permissions_updated'),
            'moderator' => [
                'is_moderator' => true,
                'permissions' => $assignment->permissionKeys(),
                'permission_options' => ModeratorPermissionCatalog::options(),
                'granted_at' => $assignment->granted_at?->translatedFormat('d M Y, H:i'),
            ],
        ]);
    }

    public function revokeModerator(Request $request, User $user): RedirectResponse|JsonResponse
    {
        $recovery = $this->moderation->revokeModerator($request->user(), $user);

        $recovered = (int) ($recovery['recovered_direct_hires'] ?? 0);
        $message = $recovered > 0
            ? __('talenma.admin.users.flash.moderator_revoked_with_recovery', ['count' => $recovered])
            : __('talenma.admin.users.flash.moderator_revoked');

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'recovery' => $recovery,
                'moderator' => [
                    'is_moderator' => false,
                    'permissions' => [],
                    'permission_options' => ModeratorPermissionCatalog::options(),
                    'granted_at' => null,
                ],
            ]);
        }

        return back()->with('moderator_revoked', true);
    }

    /**
     * @return array<string, mixed>
     */
    private function moderatorPayload(User $user): array
    {
        $assignment = $user->activeModeratorAssignment();

        return [
            'is_moderator' => $assignment !== null,
            'permissions' => $assignment?->permissionKeys() ?? [],
            'permission_options' => ModeratorPermissionCatalog::options(),
            'granted_at' => $assignment?->granted_at?->translatedFormat('d M Y, H:i'),
            'grant_url' => route('admin.users.moderator.grant', $user),
            'permissions_url' => route('admin.users.moderator.permissions', $user),
            'revoke_url' => route('admin.users.moderator.revoke', $user),
        ];
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
