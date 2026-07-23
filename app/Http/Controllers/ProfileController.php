<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\CompanyProfile;
use App\Models\User;
use App\Services\AvatarService;
use App\Services\ProfessionCatalogService;
use App\Services\UserDeletionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(
        private AvatarService $avatars,
        private UserDeletionService $userDeletion,
        private ProfessionCatalogService $professionCatalog,
    ) {}

    public function edit(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        $showCompanyPanel = $user->canManageCompanyProfile();
        $panel = $request->query('panel', 'account');

        if (! in_array($panel, ['account', 'company'], true)) {
            $panel = 'account';
        }

        if ($panel === 'company' && ! $showCompanyPanel) {
            return redirect()->route('profile.edit', ['panel' => 'account']);
        }

        $data = [
            'user' => $user,
            'panel' => $panel,
            'showCompanyPanel' => $showCompanyPanel,
        ];

        if ($showCompanyPanel && $panel === 'company') {
            $profile = $user->companyProfile ?: $user->companyProfile()->create([
                'country' => CompanyProfile::DEFAULT_COUNTRY,
            ]);

            if ($user->company_seat !== User::SEAT_OWNER) {
                $user->update(['company_seat' => User::SEAT_OWNER]);
            }

            $profile->load(['memberships.user']);

            $data = array_merge($data, [
                'profile' => $profile,
                'memberships' => $profile->memberships,
                'professionSectors' => $this->professionCatalog->sectorsForLocale(),
                'sectorSlug' => old('sector', $this->professionCatalog->sectorSlugFromLabel($profile->sector) ?? ''),
                'employeeCountOptions' => $this->employeeCountOptions(),
                'countryOptions' => CompanyProfile::countryOptions(),
                'citiesByCountry' => CompanyProfile::citiesByCountry(),
            ]);
        }

        return view('profile.edit', $data);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        if ($request->boolean('remove_avatar')) {
            $this->avatars->delete($user);
        }

        if ($request->hasFile('avatar')) {
            $this->avatars->store($user, $request->file('avatar'));
        }

        if ($user->isCompanyMember()) {
            $orgName = $user->companyOrganization()?->displayName() ?: $user->name;
            $person = trim($validated['first_name'].' '.$validated['last_name']);
            $user->fill([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'name' => $orgName.' / '.$person,
                'email' => $validated['email'],
            ]);
        } else {
            $user->fill([
                'name' => $validated['name'],
                'email' => $validated['email'],
            ]);
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit', $this->accountRedirectParams($user))
            ->with('status', 'profile-updated');
    }

    public function updateContact(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user->isCompanyOwner(), 403);

        $validated = $request->validate([
            'representative_name' => ['required', 'string', 'min:2', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'linkedin_url' => [
                'nullable',
                'url',
                'max:255',
                'regex:/^https?:\/\/([a-z0-9-]+\.)*linkedin\.com(\/|$)/i',
            ],
        ], [
            'representative_name.required' => __('talenma.company.representative_name_required'),
            'linkedin_url.url' => __('talenma.company.linkedin_invalid'),
            'linkedin_url.regex' => __('talenma.company.linkedin_host'),
        ], [
            'representative_name' => __('talenma.company.contact_full_name'),
            'phone' => __('talenma.talent.phone'),
            'linkedin_url' => __('talenma.company.linkedin'),
        ]);

        $profile = $user->companyProfile()->firstOrCreate([
            'user_id' => $user->id,
        ], [
            'country' => CompanyProfile::DEFAULT_COUNTRY,
        ]);

        $profile->update([
            'representative_name' => $validated['representative_name'],
            'phone' => $validated['phone'] ?? null,
            'linkedin_url' => $validated['linkedin_url'] ?? null,
        ]);

        return Redirect::route('profile.edit', ['panel' => 'account'])
            ->with('status', 'contact-updated');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $this->userDeletion->delete($user);

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * @return array{panel?: string}
     */
    private function accountRedirectParams(User $user): array
    {
        return $user->canManageCompanyProfile()
            ? ['panel' => 'account']
            : [];
    }

    /**
     * @return array<string, string>
     */
    private function employeeCountOptions(): array
    {
        return [
            '1-10' => '1-10',
            '11-50' => '11-50',
            '51-200' => '51-200',
            '200+' => '200+',
        ];
    }
}
