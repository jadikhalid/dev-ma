<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PendingRegistration;
use App\Services\PendingRegistrationService;
use App\Support\PortalHost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CancelPendingRegistrationController extends Controller
{
    /**
     * Confirmation page — never purge on GET (email scanners prefetch links).
     */
    public function show(string $token): View|RedirectResponse
    {
        $pending = PendingRegistration::query()->where('token', $token)->first();

        if (! $pending || $pending->isCompleted()) {
            return $this->redirectFailure(__('talenma.auth.registration_cancel_invalid'));
        }

        if ($pending->isExpired()) {
            app(PendingRegistrationService::class)->purge($pending);

            return $this->redirectFailure(__('talenma.auth.registration_cancel_expired'));
        }

        return view('auth.cancel-pending-registration', [
            'token' => $pending->token,
            'email' => $pending->email,
        ]);
    }

    /**
     * Confirmed cancellation (POST only).
     */
    public function destroy(Request $request, string $token, PendingRegistrationService $service): RedirectResponse
    {
        try {
            $service->cancelByToken($token);
        } catch (ValidationException $exception) {
            $message = collect($exception->errors())->flatten()->first()
                ?? __('talenma.auth.registration_cancel_invalid');

            return $this->redirectFailure($message);
        }

        $request->session()->forget('pending_registration_email');

        return $this->redirectSuccess(__('talenma.auth.registration_cancelled'));
    }

    private function redirectFailure(string $message): RedirectResponse
    {
        return redirect()
            ->route(PortalHost::isCompanyHost() ? 'company.offer' : 'login')
            ->with('toast_error', $message);
    }

    private function redirectSuccess(string $message): RedirectResponse
    {
        return redirect()
            ->route(PortalHost::isCompanyHost() ? 'company.offer' : 'login')
            ->with('toast_success', $message);
    }
}
