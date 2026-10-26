<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\GoogleTalentAuthService;
use App\Support\PortalHost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Throwable;

class GoogleAuthController extends Controller
{
    public function __construct(
        private GoogleTalentAuthService $googleTalentAuth,
    ) {}

    public function redirect(Request $request): RedirectResponse
    {
        if (PortalHost::isCompanyHost($request)) {
            return redirect()->route('company.offer');
        }

        $intent = $request->query('intent') === 'register' ? 'register' : 'login';
        $request->session()->put('google_auth_intent', $intent);

        $google = $this->google();

        if ($intent === 'login') {
            $google->with(['prompt' => 'select_account']);
        }

        return $google->redirect();
    }

    public function callback(Request $request): RedirectResponse
    {
        if (PortalHost::isCompanyHost($request)) {
            return redirect()->route('company.offer');
        }

        $create = $request->session()->pull('google_auth_intent') === 'register';
        $back = $create ? 'register' : 'login';

        try {
            $user = $this->googleTalentAuth->authenticate($this->google()->user(), $create);
        } catch (ValidationException $exception) {
            return redirect()
                ->route($back)
                ->withErrors($exception->errors());
        } catch (InvalidStateException) {
            return redirect()
                ->route($back)
                ->withErrors(['email' => __('talenma.auth.google_failed')]);
        } catch (Throwable $exception) {
            report($exception);

            return redirect()
                ->route($back)
                ->withErrors(['email' => __('talenma.auth.google_failed')]);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route($user->homeRouteName(), absolute: false));
    }

    private function google(): \Laravel\Socialite\Contracts\Provider
    {
        return Socialite::driver('google')
            ->scopes(['openid', 'email', 'profile'])
            ->redirectUrl(route('auth.google.callback'));
    }
}
