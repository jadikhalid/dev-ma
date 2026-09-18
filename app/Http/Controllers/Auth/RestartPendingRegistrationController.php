<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\PendingRegistrationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RestartPendingRegistrationController extends Controller
{
    public function __invoke(Request $request, PendingRegistrationService $service): RedirectResponse
    {
        $sessionEmail = $request->session()->get('pending_registration_email');

        $validated = $request->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        $email = strtolower(trim($validated['email']));

        if (! is_string($sessionEmail) || strtolower(trim($sessionEmail)) !== $email) {
            $request->session()->forget('pending_registration_email');

            return redirect()
                ->route('register')
                ->with('toast_error', __('talenma.auth.registration_restart_unavailable'));
        }

        $service->purgeForEmail($email);
        $request->session()->forget('pending_registration_email');

        return redirect()
            ->route('register')
            ->with('toast_success', __('talenma.auth.registration_restarted'));
    }
}
