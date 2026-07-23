<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\CompanyProfile;
use App\Models\User;
use App\Services\AvatarService;
use App\Services\PendingEmailChangeService;
use App\Services\ProfessionCatalogService;
use App\Services\UserDeletionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class ProfileController extends Controller
{
    public function __construct(
        private AvatarService $avatars,
        private UserDeletionService $userDeletion,
        private ProfessionCatalogService $professionCatalog,
        private PendingEmailChangeService $pendingEmailChange,
    ) {}

    public function edit(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($this->pendingEmailChange->clearExpired($user)) {
            $user->refresh();
        }

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

        if ($showCompanyPanel) {
            $profile = $user->companyProfile ?: $user->companyProfile()->create([
                'country' => CompanyProfile::DEFAULT_COUNTRY,
            ]);

            if ($user->company_seat !== User::SEAT_OWNER) {
                $user->update(['company_seat' => User::SEAT_OWNER]);
            }

            $profile->load(['memberships.user']);

            $data['profile'] = $profile;
            $data['memberships'] = $profile->memberships;

            if ($panel === 'company') {
                $data = array_merge($data, [
                    'professionSectors' => $this->professionCatalog->sectorsForLocale(),
                    'sectorSlug' => old('sector', $this->professionCatalog->sectorSlugFromLabel($profile->sector) ?? ''),
                    'employeeCountOptions' => $this->employeeCountOptions(),
                    'countryOptions' => CompanyProfile::countryOptions(),
                    'citiesByCountry' => CompanyProfile::citiesByCountry(),
                ]);
            }
        }

        return view('profile.edit', $data);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse|JsonResponse
    {
        $user = $request->user();
        $validated = $request->validated();
        $redirectParams = $this->accountRedirectParams($user);

        if ($request->boolean('remove_avatar')) {
            $this->avatars->delete($user);
        }

        if ($request->hasFile('avatar')) {
            $this->avatars->store($user, $request->file('avatar'));
        }

        $emailChanged = false;
        $newEmail = strtolower(trim((string) $validated['email']));

        if ($user->isCompanyMember()) {
            $orgName = $user->companyOrganization()?->displayName() ?: $user->name;
            $person = trim($validated['first_name'].' '.$validated['last_name']);
            $user->fill([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'name' => $orgName.' / '.$person,
                'email' => $validated['email'],
            ]);

            if ($user->isDirty('email')) {
                $user->email_verified_at = null;
            }
        } elseif ($user->isCompanyOwner()) {
            $user->fill([
                'name' => $validated['name'],
            ]);

            if ($newEmail !== strtolower($user->email)) {
                $emailChanged = true;
            }
        } else {
            $user->fill([
                'name' => $validated['name'],
                'email' => $validated['email'],
            ]);

            if ($user->isDirty('email')) {
                $user->email_verified_at = null;
            }
        }

        $user->save();

        if ($emailChanged && $user->isCompanyOwner()) {
            try {
                $this->pendingEmailChange->request($user->fresh(), $newEmail);
            } catch (ValidationException $e) {
                throw $e;
            } catch (Throwable) {
                if ($request->wantsJson()) {
                    return response()->json(['message' => __('talenma.account.pending_email_failed')], 422);
                }

                return Redirect::route('profile.edit', $redirectParams)
                    ->with('toast_error', __('talenma.account.pending_email_failed'));
            }

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => __('talenma.account.pending_email_sent'),
                    'reload' => true,
                ]);
            }

            return Redirect::route('profile.edit', $redirectParams)
                ->with('toast_success', __('talenma.account.pending_email_sent'));
        }

        if ($request->wantsJson()) {
            return response()->json([
                'message' => __('talenma.account.saved'),
                'avatar_url' => $user->fresh()->avatarUrl(),
            ]);
        }

        return Redirect::route('profile.edit', $redirectParams)
            ->with('status', 'profile-updated')
            ->with('toast_success', __('talenma.account.saved'));
    }

    public function confirmPendingEmail(string $token): RedirectResponse
    {
        $result = $this->pendingEmailChange->confirm($token);

        if (isset($result['error'])) {
            $redirect = $this->pendingEmailRedirect($result['user_id'] ?? null);

            return $redirect->with('toast_error', $result['error']);
        }

        /** @var User $user */
        $user = $result['user'];

        if (Auth::check() && (int) Auth::id() === (int) $user->id) {
            return Redirect::route('profile.edit', ['panel' => 'account'])
                ->with('toast_success', __('talenma.account.pending_email_confirmed'));
        }

        return Redirect::route('login')
            ->with('toast_success', __('talenma.account.pending_email_confirmed'));
    }

    public function cancelPendingEmail(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user->isCompanyOwner(), 403);

        $this->pendingEmailChange->clear($user, 'cancelled');

        return Redirect::route('profile.edit', ['panel' => 'account'])
            ->with('toast_success', __('talenma.account.pending_email_cancelled'));
    }

    private function pendingEmailRedirect(?int $userId): RedirectResponse
    {
        if (Auth::check() && $userId && (int) Auth::id() === $userId) {
            return Redirect::route('profile.edit', ['panel' => 'account']);
        }

        if (Auth::check() && Auth::user()?->isCompanyOwner()) {
            return Redirect::route('profile.edit', ['panel' => 'account']);
        }

        return Redirect::route('login');
    }

    public function updateContact(Request $request): RedirectResponse|JsonResponse
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

        if ($request->wantsJson()) {
            return response()->json([
                'message' => __('talenma.account.contact_saved'),
            ]);
        }

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
        return $user->canManageCompanyProfile() || $user->isCompanyOwner()
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
