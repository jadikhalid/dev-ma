<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\JobPosting;
use App\Models\PendingRegistration;
use App\Models\PlatformSetting;
use App\Models\User;
use App\Services\PendingRegistrationService;
use App\Services\ProfessionCatalogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
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

        $fromJobId = (int) request()->integer('from_job');
        if ($fromJobId > 0) {
            $job = JobPosting::query()->find($fromJobId);
            if ($job?->isPublished() && ! $job->isClosed()) {
                session(['url.intended' => route('jobs.gate', $job)]);
            }
        }

        return view('auth.register', [
            'defaultRole' => $defaultRole,
            'professionSectors' => $this->professionCatalog->sectorsForLocale(),
            'companyCountryOptions' => \App\Models\CompanyProfile::countryOptions(),
        ]);
    }

    public function checkEmail(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class, 'email'),
                Rule::unique(User::class, 'pending_email'),
                Rule::unique(PendingRegistration::class, 'email'),
            ],
        ], [
            'email.required' => __('talenma.auth.validation.email_required'),
            'email.email' => __('talenma.auth.validation.email_invalid'),
            'email.unique' => __('talenma.auth.validation.email_taken'),
        ], [
            'email' => __('talenma.auth.email'),
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->get('email');

            return response()->json([
                'available' => false,
                'message' => $messages[0] ?? __('talenma.auth.validation.email_taken'),
            ], 422);
        }

        return response()->json([
            'available' => true,
            'message' => __('talenma.auth.validation.email_available'),
        ]);
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        $email = (string) $request->validated('email');
        $role = (string) $request->validated('role');
        $skipTalentEmailVerification = $role === 'dev'
            && ! PlatformSetting::requiresTalentEmailVerification();

        try {
            if ($skipTalentEmailVerification) {
                $user = $this->pendingRegistration->registerTalentImmediately($request);
            } else {
                $this->pendingRegistration->createFromRequest($request);
            }
        } catch (Throwable $exception) {
            report($exception);

            return redirect()
                ->route('register')
                ->withInput($request->except('password', 'password_confirmation', 'documents', 'cv'))
                ->with(
                    'toast_error',
                    $skipTalentEmailVerification
                        ? __('talenma.auth.register_failed')
                        : __('talenma.auth.verification_email_failed')
                );
        }

        $request->clearRateLimiter();

        if ($skipTalentEmailVerification) {
            Auth::login($user);
            $request->session()->regenerate();
            $request->session()->flash(
                'toast_sticky_success',
                __('talenma.auth.registration_welcome_complete_profile')
            );

            return redirect()->route('profile.edit', ['panel' => 'talent']);
        }

        $request->session()->put('pending_registration_email', $email);

        return redirect()
            ->route('login')
            ->with('toast_success', __('talenma.auth.register_success_verify'));
    }
}
