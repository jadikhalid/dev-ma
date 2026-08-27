<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\CompanyProfileController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ProfileDetailsController;
use App\Models\CompanyProfile;
use App\Models\User;
use App\Services\AvatarService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ManagedProfileController extends Controller
{
    public function __construct(
        private ProfileDetailsController $talentProfiles,
        private CompanyProfileController $companyProfiles,
        private AvatarService $avatars,
    ) {}

    public function edit(User $user): View|RedirectResponse
    {
        abort_unless($user->isTalent() || $user->isCompany(), 404);
        abort_if($user->isAdmin(), 404);

        if ($user->isTalent()) {
            return view('admin.users.edit-profile', array_merge(
                $this->talentProfiles->panelData($user),
                [
                    'user' => $user,
                    'targetUser' => $user,
                    'profileKind' => 'talent',
                    'profileUpdateUrl' => route('admin.users.profile.update', $user),
                    'avatarUpdateUrl' => route('admin.users.profile.avatar', $user),
                    'isAdminProfileEdit' => true,
                    'talentVideoEditable' => false,
                ],
            ));
        }

        $org = $this->resolveCompanyProfile($user);

        if (! $org) {
            return redirect()
                ->route('admin.users.index', ['filter' => 'companies'])
                ->with('toast_error', __('talenma.admin.users.edit_profile_company_org_missing'));
        }

        return view('admin.users.edit-profile', array_merge(
            $this->companyProfiles->panelData($org),
            [
                'user' => $org->user ?? $user,
                'targetUser' => $user,
                'profileKind' => 'company',
                'companyProfileUpdateUrl' => route('admin.users.profile.update', $user),
                'isAdminProfileEdit' => true,
            ],
        ));
    }

    public function update(Request $request, User $user): RedirectResponse|JsonResponse
    {
        abort_unless($user->isTalent() || $user->isCompany(), 404);
        abort_if($user->isAdmin(), 404);

        $redirect = route('admin.users.profile.edit', $user);

        if ($user->isTalent()) {
            return $this->talentProfiles->updateForUser($request, $user, $redirect);
        }

        $org = $this->resolveCompanyProfile($user);

        abort_unless($org, 404);

        return $this->companyProfiles->updateForProfile($request, $org, $redirect);
    }

    public function updateAvatar(Request $request, User $user): RedirectResponse|JsonResponse
    {
        abort_unless($user->isTalent(), 404);
        abort_if($user->isAdmin(), 404);

        $request->validate([
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_avatar' => ['nullable', 'boolean'],
        ], [], [
            'avatar' => __('talenma.account.avatar'),
        ]);

        if ($request->boolean('remove_avatar')) {
            $this->avatars->delete($user);
        }

        if ($request->hasFile('avatar')) {
            $this->avatars->store($user, $request->file('avatar'));
        }

        $message = __('talenma.admin.users.edit_profile_saved');
        $fresh = $user->fresh();

        if ($request->wantsJson()) {
            return response()->json([
                'message' => $message,
                'avatar_url' => $fresh->avatarUrl(),
                'avatar_initials' => $fresh->initials(),
            ]);
        }

        return redirect()
            ->route('admin.users.profile.edit', $user)
            ->with('toast_success', $message);
    }

    private function resolveCompanyProfile(User $user): ?CompanyProfile
    {
        $org = $user->companyOrganization();

        if ($org) {
            return $org;
        }

        if ($user->isCompanyOwner()) {
            return $user->companyProfile()->firstOrCreate(['user_id' => $user->id]);
        }

        return null;
    }
}
