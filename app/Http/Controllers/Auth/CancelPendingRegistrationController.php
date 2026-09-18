<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\PendingRegistrationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;

class CancelPendingRegistrationController extends Controller
{
    public function __invoke(string $token, PendingRegistrationService $service): RedirectResponse
    {
        try {
            $service->cancelByToken($token);
        } catch (ValidationException $exception) {
            $message = collect($exception->errors())->flatten()->first()
                ?? __('talenma.auth.registration_cancel_invalid');

            return redirect()
                ->route('login')
                ->with('toast_error', $message);
        }

        request()->session()->forget('pending_registration_email');

        return redirect()
            ->route('login')
            ->with('toast_success', __('talenma.auth.registration_cancelled'));
    }
}
