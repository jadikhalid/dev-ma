<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\PendingRegistrationService;
use App\Services\ProfessionCatalogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Throwable;

class RegisteredUserController extends Controller
{
    public function __construct(
        private ProfessionCatalogService $professionCatalog,
        private PendingRegistrationService $pendingRegistration,
    ) {}

    public function create(): View
    {
        $role = request('role');
        $defaultRole = in_array($role, ['dev', 'company'], true) ? $role : '';

        return view('auth.register', [
            'defaultRole' => $defaultRole,
            'professionSectors' => $this->professionCatalog->sectorsForLocale(),
            'companyCountryOptions' => \App\Models\CompanyProfile::countryOptions(),
        ]);
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        $email = (string) $request->validated('email');

        try {
            $this->pendingRegistration->createFromRequest($request);
        } catch (Throwable $exception) {
            report($exception);

            return redirect()
                ->route('register')
                ->withInput($request->except('password', 'password_confirmation', 'documents'))
                ->with('toast_error', __('talenma.auth.verification_email_failed'));
        }

        $request->clearRateLimiter();

        $request->session()->put('pending_registration_email', $email);

        return redirect()
            ->route('login')
            ->with('toast_success', __('talenma.auth.register_success_verify'));
    }
}
