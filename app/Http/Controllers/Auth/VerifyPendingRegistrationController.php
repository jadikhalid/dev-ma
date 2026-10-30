<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\PendingRegistrationService;
use App\Support\PortalHost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class VerifyPendingRegistrationController extends Controller
{
    public function __invoke(string $token, PendingRegistrationService $service): RedirectResponse
    {
        try {
            $user = $service->complete($token);
        } catch (ValidationException $exception) {
            return $this->redirectAfterVerificationFailure($exception);
        }

        // Company sessions live only on the entreprises host.
        if ($user->isCompany() && ! PortalHost::isCompanyHost(request())) {
            return redirect()
                ->away(PortalHost::companyLoginUrl())
                ->with('toast_success', __('talenma.auth.registration_verified_approved_login'));
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

        if ($user->isCompany()) {
            return redirect()
                ->away(PortalHost::companyUrl('/dashboard'))
                ->with('toast_success', __($toastKey));
        }

        return redirect()
            ->route($user->homeRouteName())
            ->with('toast_success', __($toastKey));
    }

    private function redirectAfterVerificationFailure(ValidationException $exception): RedirectResponse
    {
        $message = collect($exception->errors())->flatten()->first()
            ?? __('talenma.auth.registration_verify_invalid');

        // Never bounce companies back to the public trial form after a verify click.
        if (PortalHost::isCompanyHost(request())) {
            return redirect()
                ->route('login')
                ->with('toast_error', $message);
        }

        return redirect()
            ->route('register')
            ->with('toast_error', $message);
    }
}
