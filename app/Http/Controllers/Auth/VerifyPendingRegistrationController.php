<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\PendingRegistrationService;
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
            $message = collect($exception->errors())->flatten()->first()
                ?? __('talenma.auth.registration_verify_invalid');

            return redirect()
                ->route('register')
                ->with('toast_error', $message);
        }

        Auth::login($user);
        request()->session()->regenerate();

        return $this->redirectAfterRegistration($user);
    }

    private function redirectAfterRegistration(User $user): RedirectResponse
    {
        return redirect()
            ->route($user->homeRouteName())
            ->with('toast_success', __('talenma.auth.registration_verified_success'));
    }
}
