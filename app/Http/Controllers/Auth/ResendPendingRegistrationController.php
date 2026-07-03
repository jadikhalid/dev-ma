<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\PendingRegistrationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Throwable;

class ResendPendingRegistrationController extends Controller
{
    public function __invoke(Request $request, PendingRegistrationService $service): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        try {
            $service->resendVerificationEmail($validated['email']);
        } catch (ValidationException $exception) {
            $message = collect($exception->errors())->flatten()->first()
                ?? __('talenma.auth.registration_resend_unavailable');

            return redirect()
                ->route('login')
                ->withInput($request->only('email'))
                ->with('toast_error', $message);
        } catch (Throwable $exception) {
            report($exception);

            return redirect()
                ->route('login')
                ->withInput($request->only('email'))
                ->with('toast_error', __('talenma.auth.verification_resend_failed'));
        }

        return redirect()
            ->route('login')
            ->with('pending_registration_email', $validated['email'])
            ->with('toast_success', __('talenma.auth.verification_link_sent'));
    }
}
