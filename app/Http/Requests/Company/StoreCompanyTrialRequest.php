<?php

namespace App\Http\Requests\Company;

use App\Models\CompanyProfile;
use App\Models\CompanyTrialRequest;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCompanyTrialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'company_name' => is_string($this->company_name)
                ? trim(preg_replace('/\s+/u', ' ', $this->company_name) ?? '')
                : $this->company_name,
            'contact_name' => is_string($this->contact_name)
                ? trim(preg_replace('/\s+/u', ' ', $this->contact_name) ?? '')
                : $this->contact_name,
            'email' => is_string($this->email) ? strtolower(trim($this->email)) : $this->email,
            'phone' => is_string($this->phone) ? trim($this->phone) : $this->phone,
            'company_description' => is_string($this->company_description)
                ? trim($this->company_description)
                : $this->company_description,
            'company_website' => is_string($this->company_website)
                ? trim($this->company_website)
                : $this->company_website,
            'company_country' => is_string($this->company_country)
                ? (trim($this->company_country) !== '' ? trim($this->company_country) : null)
                : $this->company_country,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'company_name' => [
                'required',
                'string',
                'min:2',
                'max:255',
                'regex:/^[\p{L}\p{M}][\p{L}\p{M}\s\'\-\.]*$/u',
            ],
            'contact_name' => [
                'required',
                'string',
                'min:2',
                'max:255',
                'regex:/^[\p{L}\p{M}][\p{L}\p{M}\s\'\-\.]*$/u',
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique(User::class, 'email'),
                Rule::unique(CompanyTrialRequest::class, 'email')->where(
                    fn ($query) => $query->where('status', CompanyTrialRequest::STATUS_PENDING)
                ),
            ],
            'phone' => ['required', 'string', 'max:50', 'regex:/^\+?[0-9\s\-\.\(\)]{8,20}$/'],
            'sector' => [
                'required',
                'string',
                'max:64',
                Rule::exists('profession_sectors', 'slug')->where(fn ($query) => $query->where('is_active', true)),
            ],
            'company_description' => ['required', 'string', 'min:20', 'max:5000'],
            'company_website' => ['nullable', 'url', 'max:255'],
            'company_country' => ['required', 'string', Rule::in(CompanyProfile::COUNTRY_CODES)],
            'data_processing_consent' => ['accepted'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'company_name.required' => __('talenma.company_offer.demo_company_required'),
            'contact_name.required' => __('talenma.company_offer.trial_contact_required'),
            'email.required' => __('talenma.company_offer.demo_email_required'),
            'email.email' => __('talenma.company_offer.demo_email_invalid'),
            'email.unique' => __('talenma.auth.validation.email_taken'),
            'phone.required' => __('talenma.company_offer.trial_phone_required'),
            'phone.regex' => __('talenma.company.phone_invalid'),
            'sector.required' => __('talenma.auth.validation.sector_required'),
            'sector.exists' => __('talenma.auth.validation.sector_invalid'),
            'company_description.required' => __('talenma.auth.validation.company_description_required'),
            'company_description.min' => __('talenma.auth.validation.company_description_min'),
            'company_country.required' => __('talenma.auth.validation.company_country_required'),
            'data_processing_consent.accepted' => __('talenma.auth.validation.data_processing_consent_required'),
        ];
    }
}
