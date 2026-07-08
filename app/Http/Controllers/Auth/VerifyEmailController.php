<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        $user = $request->user();
        $destination = $this->verifiedDestination($user);

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended($destination.'?verified=1');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return redirect()->intended($destination.'?verified=1');
    }

    private function verifiedDestination(\App\Models\User $user): string
    {
        return route($user->homeRouteName(), absolute: false);
    }
}
