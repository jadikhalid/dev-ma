<?php

namespace App\Http\Controllers;

use App\Http\Requests\Company\StoreCompanyTrialRequest;
use App\Mail\CompanyDemoConfirmationMail;
use App\Mail\CompanyDemoRequestMail;
use App\Mail\CompanyTrialRequestMail;
use App\Models\CompanyDemoRequest;
use App\Models\CompanyProfile;
use App\Models\CompanyTrialRequest;
use App\Models\User;
use App\Services\MessagingService;
use App\Services\ProfessionCatalogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CompanyOfferController extends Controller
{
    public function __construct(
        private MessagingService $messaging,
        private ProfessionCatalogService $professionCatalog,
    ) {}

    public function show(Request $request): View|RedirectResponse
    {
        if ($request->user()?->isCompany()) {
            return redirect()->route('dashboard');
        }

        return view('company.offer', [
            'trialMonths' => CompanyProfile::TRIAL_MONTHS,
            'priceFromUsd' => CompanyProfile::PLAN_PRICE_FROM_USD,
            'professionSectors' => $this->professionCatalog->sectorsForLocale(),
            'companyCountryOptions' => CompanyProfile::countryOptions(),
            'talentCount' => User::query()
                ->where('role', 'dev')
                ->where('approval_status', User::APPROVAL_APPROVED)
                ->count(),
            'companyCount' => CompanyProfile::query()
                ->whereHas('user', fn ($query) => $query
                    ->where('role', 'company')
                    ->where('approval_status', User::APPROVAL_APPROVED))
                ->count(),
        ]);
    }

    public function storeDemo(Request $request): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255'],
            'phone_country' => ['nullable', Rule::in(array_keys(CompanyDemoRequest::PHONE_COUNTRIES))],
            'phone' => ['required', 'string', 'regex:/^\+?[0-9\s\-\.\(\)]{6,20}$/'],
            'company_name' => ['required', 'string', 'min:2', 'max:255'],
            'company_size' => ['required', Rule::in(CompanyDemoRequest::COMPANY_SIZES)],
            'hires_planned' => ['required', Rule::in(CompanyDemoRequest::HIRES_PLANNED)],
            'hiring_locations' => ['required', 'array', 'min:1'],
            'hiring_locations.*' => ['string', Rule::in(CompanyDemoRequest::HIRING_LOCATIONS)],
            'hiring_city' => ['nullable', 'string', 'max:120'],
            'uses_ats' => ['required', Rule::in(CompanyDemoRequest::ATS_OPTIONS)],
            'preferred_date' => [
                'required',
                'date',
                'after:today',
                Rule::in(CompanyDemoRequest::availableBookingDates()->map->toDateString()->all()),
            ],
            'preferred_slots' => ['required', 'array', 'min:1'],
            'preferred_slots.*' => ['string', Rule::in(CompanyDemoRequest::SLOT_STARTS)],
            'meeting_platform' => ['required', Rule::in(CompanyDemoRequest::MEETING_PLATFORMS)],
            'message' => ['nullable', 'string', 'max:5000'],
        ], [
            'first_name.required' => __('talenma.company_offer.demo_form.first_name_required'),
            'last_name.required' => __('talenma.company_offer.demo_form.last_name_required'),
            'email.required' => __('talenma.company_offer.demo_email_required'),
            'email.email' => __('talenma.company_offer.demo_email_invalid'),
            'phone.required' => __('talenma.company_offer.demo_form.phone_required'),
            'phone.regex' => __('talenma.company.phone_invalid'),
            'company_name.required' => __('talenma.company_offer.demo_company_required'),
            'company_size.required' => __('talenma.company_offer.demo_form.company_size_required'),
            'hires_planned.required' => __('talenma.company_offer.demo_form.hires_planned_required'),
            'hiring_locations.required' => __('talenma.company_offer.demo_form.hiring_locations_required'),
            'uses_ats.required' => __('talenma.company_offer.demo_form.uses_ats_required'),
            'preferred_date.required' => __('talenma.company_offer.demo_form.preferred_date_required'),
            'preferred_date.in' => __('talenma.company_offer.demo_form.preferred_date_invalid'),
            'preferred_slots.required' => __('talenma.company_offer.demo_form.preferred_slots_required'),
            'meeting_platform.required' => __('talenma.company_offer.demo_form.meeting_platform_required'),
        ]);

        $firstName = trim($data['first_name']);
        $lastName = trim($data['last_name']);
        $phone = trim($data['phone']);
        if (! str_starts_with($phone, '+') && filled($data['phone_country'] ?? null)) {
            $phone = CompanyDemoRequest::PHONE_COUNTRIES[$data['phone_country']]['dial'].' '.ltrim($phone, '0');
        }

        $slots = collect($data['preferred_slots'])
            ->unique()
            ->sort()
            ->values()
            ->all();

        $demo = CompanyDemoRequest::query()->create([
            'company_name' => trim($data['company_name']),
            'company_size' => $data['company_size'],
            'contact_name' => trim($firstName.' '.$lastName),
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => strtolower(trim($data['email'])),
            'phone' => $phone,
            'hires_planned' => $data['hires_planned'],
            'hiring_locations' => array_values(array_unique($data['hiring_locations'])),
            'hiring_city' => filled($data['hiring_city'] ?? null) ? trim($data['hiring_city']) : null,
            'uses_ats' => $data['uses_ats'],
            'preferred_date' => $data['preferred_date'],
            'preferred_slots' => $slots,
            'meeting_platform' => $data['meeting_platform'],
            'message' => filled($data['message'] ?? null) ? trim($data['message']) : null,
            'locale' => app()->getLocale(),
            'ip_address' => $request->ip(),
        ]);

        try {
            $admin = $this->messaging->resolveAdminRecipient();
            Mail::to($admin->email)->send(new CompanyDemoRequestMail($demo));
        } catch (\Throwable $e) {
            report($e);
        }

        try {
            Mail::to($demo->email)->locale($demo->locale)->send(new CompanyDemoConfirmationMail($demo));
        } catch (\Throwable $e) {
            report($e);
        }

        $message = __('talenma.company_offer.demo_sent');

        if ($request->expectsJson()) {
            return response()->json(['message' => $message]);
        }

        return redirect()
            ->to(route('company.offer', ['tab' => 'demo']).'#demo')
            ->with('toast_success', $message);
    }

    public function storeTrial(StoreCompanyTrialRequest $request): RedirectResponse|JsonResponse
    {
        $data = $request->validated();

        $trial = CompanyTrialRequest::query()->create([
            'company_name' => $data['company_name'],
            'contact_name' => $data['contact_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'sector' => $data['sector'],
            'company_description' => $data['company_description'],
            'company_website' => filled($data['company_website'] ?? null) ? $data['company_website'] : null,
            'company_country' => $data['company_country'],
            'status' => CompanyTrialRequest::STATUS_PENDING,
            'locale' => app()->getLocale(),
            'ip_address' => $request->ip(),
        ]);

        try {
            $admin = $this->messaging->resolveAdminRecipient();
            Mail::to($admin->email)->send(new CompanyTrialRequestMail($trial));
        } catch (\Throwable) {
            // Demande conservée en base même sans boîte admin disponible.
        }

        $message = __('talenma.company_offer.trial_sent');

        if ($request->expectsJson()) {
            return response()->json(['message' => $message]);
        }

        return redirect()
            ->to(route('company.offer', ['tab' => 'trial']).'#trial')
            ->with('toast_success', $message);
    }
}
