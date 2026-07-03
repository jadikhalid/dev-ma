<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\ProfessionSector;
use App\Models\User;
use App\Services\ProfessionCatalogService;
use App\Services\ProfileDocumentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Throwable;

class RegisteredUserController extends Controller
{
    public function __construct(
        private ProfessionCatalogService $professionCatalog,
        private ProfileDocumentService $documentService,
    ) {}

    public function create(): View
    {
        $role = request('role');
        $defaultRole = in_array($role, ['dev', 'company'], true) ? $role : '';

        return view('auth.register', [
            'defaultRole' => $defaultRole,
            'professionSectors' => $this->professionCatalog->sectorsForLocale(),
        ]);
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => $validated['role'],
            'approval_status' => $validated['role'] === 'dev' ? User::APPROVAL_PENDING : User::APPROVAL_APPROVED,
            'approved_at' => $validated['role'] === 'company' ? now() : null,
        ]);

        if ($user->role === 'company') {
            $user->companyProfile()->create([
                'company_name' => $validated['name'],
                'representative_name' => $validated['representative_name'],
                'representative_email' => $validated['representative_email'],
                'hiring_needs' => $validated['company_need'],
                'country' => 'France',
            ]);
        }

        if ($user->role === 'dev') {
            $sector = ProfessionSector::query()
                ->where('slug', $validated['sector'])
                ->where('is_active', true)
                ->firstOrFail();

            $profile = $user->profile()->create([
                'profession_sector_id' => $sector->id,
                'registration_description' => $validated['description'],
                'experience_years' => 0,
                'country' => 'Maroc',
            ]);

            $this->documentService->storeMany($profile, $request->file('documents', []));
        }

        $request->clearRateLimiter();

        try {
            $user->sendEmailVerificationNotification();
        } catch (Throwable $exception) {
            report($exception);

            Auth::login($user);
            $request->session()->regenerate();

            return redirect()
                ->route('verification.notice')
                ->with('toast_error', __('talenma.auth.verification_email_failed'));
        }

        return redirect()
            ->route('login')
            ->with('toast_success', __('talenma.auth.register_success'));
    }
}
