<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PendingRegistration;
use App\Models\User;
use App\Services\PendingRegistrationService;
use App\Support\PortalHost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class VerifyPendingRegistrationController extends Controller
{
    public function __invoke(string $token, PendingRegistrationService $service): RedirectResponse
    {
        $pending = PendingRegistration::query()->where('token', $token)->first();

        // Company sessions are host-only. Never finalize on the talents host:
        // email scanners often open www first and break signed query redirects.
        if ($pending && $pending->isCompanyRegistration() && ! PortalHost::isCompanyHost(request())) {
            return redirect()->away(PortalHost::companyUrl('/register/verify/'.$token));
        }

        try {
            $user = $service->complete($token);
        } catch (ValidationException $exception) {
            return $this->redirectAfterVerificationFailure($exception);
        }

        if ($user->isCompany() && ! PortalHost::isCompanyHost(request())) {
            $code = Str::random(40);
            Cache::put('company_reg_finish:'.$code, $user->id, now()->addHour());

            return redirect()->away(PortalHost::companyUrl('/register/company-finish/'.$code));
        }

        Auth::login($user);
        request()->session()->regenerate();
        request()->session()->forget('pending_registration_email');

        if ($user->isTalent()) {
            request()->session()->flash(
                'toast_sticky_success',
                __('talenma.auth.registration_welcome_complete_profile')
            );

            if ($user->canAccessWorkspaceApps() && in_array(request()->session()->get('url.intended'), [route('cv-builder.gate'), route('ats-score.gate'), route('library.gate')], true)) {
                return redirect()->intended(route('profile.edit', ['panel' => 'talent']));
            }

            return redirect()->route('profile.edit', ['panel' => 'talent']);
        }

        return $this->redirectAfterRegistration($user);
    }

    private function redirectAfterRegistration(User $user): RedirectResponse
    {
        $toastKey = $user->isApproved()
            ? 'talenma.auth.registration_verified_approved'
            : 'talenma.auth.registration_verified_success';

        // Relative path keeps the session on the current host (entreprises vs www).
        return redirect()
            ->to(route($user->homeRouteName(), absolute: false))
            ->with('toast_success', __($toastKey));
    }

    private function redirectAfterVerificationFailure(ValidationException $exception): RedirectResponse
    {
        $message = collect($exception->errors())->flatten()->first()
            ?? __('talenma.auth.registration_verify_invalid');

        if (PortalHost::isCompanyHost(request())) {
            return redirect()
                ->to(route('login', absolute: false))
                ->with('toast_error', $message);
        }

        return redirect()
            ->to(route('register', absolute: false))
            ->with('toast_error', $message);
    }
}
