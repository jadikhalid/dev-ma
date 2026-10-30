<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\PortalHost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class FinishCompanyRegistrationController extends Controller
{
    /**
     * Complete company registration login on the entreprises host
     * after the verification link was opened on another host.
     */
    public function __invoke(Request $request, User $user): RedirectResponse
    {
        abort_unless($request->hasValidSignature(), 403);
        abort_unless($user->isCompany(), 404);

        if (! PortalHost::isCompanyHost($request)) {
            $finishUrl = PortalHost::onCompanyHost(
                fn () => URL::temporarySignedRoute(
                    'register.company-finish',
                    now()->addHour(),
                    ['user' => $user->id],
                    absolute: true,
                )
            );

            return redirect()->away($finishUrl);
        }

        Auth::login($user);
        $request->session()->regenerate();
        $request->session()->forget('pending_registration_email');

        $toastKey = $user->isApproved()
            ? 'talenma.auth.registration_verified_approved'
            : 'talenma.auth.registration_verified_success';

        return redirect()
            ->route('dashboard')
            ->with('toast_success', __($toastKey));
    }
}
