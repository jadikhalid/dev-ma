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

        if ($user->isTalent() && $request->input('section') === 'identity') {
            return $this->updateTalentIdentity($request, $user, $redirect);
        }

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

    private function updateTalentIdentity(Request $request, User $user, string $redirect): RedirectResponse|JsonResponse
    {
        $firstName = is_string($request->input('first_name'))
            ? trim((string) preg_replace('/\s+/u', ' ', $request->input('first_name')))
            : $request->input('first_name');
        $lastName = is_string($request->input('last_name'))
            ? trim((string) preg_replace('/\s+/u', ' ', $request->input('last_name')))
            : $request->input('last_name');

        $request->merge([
            'first_name' => $firstName,
            'last_name' => $lastName,
        ]);

        $personNameRules = [
            'required',
            'string',
            'min:2',
            'max:127',
            'regex:/^[\p{L}\p{M}][\p{L}\p{M}\s\'\-\.]*$/u',
        ];

        $data = $request->validate([
            'first_name' => $personNameRules,
            'last_name' => $personNameRules,
        ], [
            'first_name.required' => __('talenma.auth.validation.first_name_required'),
            'last_name.required' => __('talenma.auth.validation.last_name_required'),
            'first_name.min' => __('talenma.auth.validation.first_name_min'),
            'last_name.min' => __('talenma.auth.validation.last_name_min'),
            'first_name.max' => __('talenma.auth.validation.first_name_max'),
            'last_name.max' => __('talenma.auth.validation.last_name_max'),
            'first_name.regex' => __('talenma.auth.validation.first_name_format'),
            'last_name.regex' => __('talenma.auth.validation.last_name_format'),
        ], [
            'first_name' => __('talenma.auth.first_name'),
            'last_name' => __('talenma.auth.last_name'),
        ]);

        $user->fill([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'name' => trim($data['first_name'].' '.$data['last_name']),
        ])->save();

        $message = __('talenma.admin.users.edit_profile_saved');

        if ($request->wantsJson()) {
            return response()->json([
                'message' => $message,
                'reload' => true,
            ]);
        }

        return redirect()
            ->to($redirect)
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
