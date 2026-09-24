<?php

namespace App\Http\Controllers;

use App\Http\Requests\Company\StoreCompanyTrialRequest;
use App\Mail\CompanyDemoRequestMail;
use App\Mail\CompanyTrialRequestMail;
use App\Models\CompanyDemoRequest;
use App\Models\CompanyProfile;
use App\Models\CompanyTrialRequest;
use App\Services\MessagingService;
use App\Services\ProfessionCatalogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class CompanyOfferController extends Controller
{
    public function __construct(
        private MessagingService $messaging,
        private ProfessionCatalogService $professionCatalog,
    ) {}

    public function show(): View
    {
        return view('company.offer', [
            'trialMonths' => CompanyProfile::TRIAL_MONTHS,
            'priceFromUsd' => CompanyProfile::PLAN_PRICE_FROM_USD,
            'professionSectors' => $this->professionCatalog->sectorsForLocale(),
            'companyCountryOptions' => CompanyProfile::countryOptions(),
        ]);
    }

    public function storeDemo(Request $request): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'company_name' => ['required', 'string', 'min:2', 'max:255'],
            'contact_name' => ['required', 'string', 'min:2', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'message' => ['required', 'string', 'min:20', 'max:5000'],
        ], [
            'company_name.required' => __('talenma.company_offer.demo_company_required'),
            'contact_name.required' => __('talenma.company_offer.demo_contact_required'),
            'email.required' => __('talenma.company_offer.demo_email_required'),
            'email.email' => __('talenma.company_offer.demo_email_invalid'),
            'message.required' => __('talenma.company_offer.demo_message_required'),
            'message.min' => __('talenma.company_offer.demo_message_min'),
        ]);

        $demo = CompanyDemoRequest::query()->create([
            'company_name' => trim($data['company_name']),
            'contact_name' => trim($data['contact_name']),
            'email' => strtolower(trim($data['email'])),
            'phone' => filled($data['phone'] ?? null) ? trim($data['phone']) : null,
            'message' => trim($data['message']),
            'locale' => app()->getLocale(),
            'ip_address' => $request->ip(),
        ]);

        try {
            $admin = $this->messaging->resolveAdminRecipient();
            Mail::to($admin->email)->send(new CompanyDemoRequestMail($demo));
        } catch (\Throwable) {
            // Demande conservée en base même sans boîte admin disponible.
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
