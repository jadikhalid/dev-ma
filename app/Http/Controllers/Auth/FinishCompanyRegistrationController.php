<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\PortalHost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class FinishCompanyRegistrationController extends Controller
{
    /**
     * Consume a one-time code and log the company in on the entreprises host.
     */
    public function __invoke(Request $request, string $code): RedirectResponse
    {
        if (! PortalHost::isCompanyHost($request)) {
            return redirect()->away(PortalHost::companyUrl('/register/company-finish/'.$code));
        }

        $userId = Cache::pull('company_reg_finish:'.$code);

        if (! $userId) {
            return redirect()
                ->route('login')
                ->with('toast_error', __('talenma.auth.registration_verify_invalid'));
        }

        $user = User::query()->find($userId);

        if (! $user?->isCompany()) {
            return redirect()
                ->route('login')
                ->with('toast_error', __('talenma.auth.registration_verify_invalid'));
        }

        Auth::login($user);
        $request->session()->regenerate();
        $request->session()->forget('pending_registration_email');

        $toastKey = $user->isApproved()
            ? 'talenma.auth.registration_verified_approved'
            : 'talenma.auth.registration_verified_success';

        return redirect()
            ->to(route('dashboard', absolute: false))
            ->with('toast_success', __($toastKey));
    }
}
