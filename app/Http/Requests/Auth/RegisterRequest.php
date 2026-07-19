<?php

namespace App\Http\Requests\Auth;

use App\Models\PendingRegistration;
use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => is_string($this->name) ? trim(preg_replace('/\s+/u', ' ', $this->name) ?? '') : $this->name,
            'first_name' => is_string($this->first_name)
                ? trim(preg_replace('/\s+/u', ' ', $this->first_name) ?? '')
                : $this->first_name,
            'last_name' => is_string($this->last_name)
                ? trim(preg_replace('/\s+/u', ' ', $this->last_name) ?? '')
                : $this->last_name,
            'email' => is_string($this->email) ? Str::lower(trim($this->email)) : $this->email,
            'representative_name' => is_string($this->representative_name)
                ? trim(preg_replace('/\s+/u', ' ', $this->representative_name) ?? '')
                : $this->representative_name,
            'representative_email' => is_string($this->representative_email)
                ? Str::lower(trim($this->representative_email))
                : $this->representative_email,
            'company_need' => is_string($this->company_need) ? trim($this->company_need) : $this->company_need,
            'company_website' => is_string($this->company_website) ? trim($this->company_website) : $this->company_website,
            'company_country' => is_string($this->company_country) ? trim($this->company_country) : $this->company_country,
        ]);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                Rule::requiredIf(fn () => $this->input('role') === 'company'),
                'nullable',
                'string',
                'min:2',
                'max:255',
                'regex:/^[\p{L}\p{M}][\p{L}\p{M}\s\'\-\.]*$/u',
            ],
            'first_name' => [
                Rule::requiredIf(fn () => $this->input('role') === 'dev'),
                'nullable',
                'string',
                'min:2',
                'max:127',
                'regex:/^[\p{L}\p{M}][\p{L}\p{M}\s\'\-\.]*$/u',
            ],
            'last_name' => [
                Rule::requiredIf(fn () => $this->input('role') === 'dev'),
                'nullable',
                'string',
                'min:2',
                'max:127',
                'regex:/^[\p{L}\p{M}][\p{L}\p{M}\s\'\-\.]*$/u',
            ],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class),
                Rule::unique(PendingRegistration::class),
            ],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string', 'in:dev,company'],
            'website' => ['prohibited'],
            'sector' => [
                Rule::requiredIf(fn () => in_array($this->input('role'), ['dev', 'company'], true)),
                'nullable',
                'string',
                'max:64',
                Rule::exists('profession_sectors', 'slug')->where(fn ($query) => $query->where('is_active', true)),
            ],
            'description' => [
                Rule::requiredIf(fn () => $this->input('role') === 'dev'),
                'nullable',
                'string',
                'min:255',
                'max:2550',
            ],
            'documents' => [
                Rule::requiredIf(fn () => $this->input('role') === 'dev'),
                'nullable',
                'array',
                Rule::when($this->input('role') === 'dev', 'min:1'),
                Rule::when($this->input('role') === 'dev', 'max:5'),
                Rule::when($this->input('role') === 'company', 'max:2'),
            ],
            'documents.*' => [
                'file',
                'max:1024',
                'mimes:pdf,jpg,jpeg,png,webp',
            ],
            'representative_name' => [
                Rule::requiredIf(fn () => $this->input('role') === 'company'),
                'nullable',
                'string',
                'min:2',
                'max:255',
                'regex:/^[\p{L}\p{M}][\p{L}\p{M}\s\'\-\.]*$/u',
            ],
            'representative_email' => [
                Rule::requiredIf(fn () => $this->input('role') === 'company'),
                'nullable',
                'string',
                'lowercase',
                'email',
                'max:255',
            ],
            'company_need' => [
                Rule::requiredIf(fn () => $this->input('role') === 'company'),
                'nullable',
                'string',
                'min:20',
                'max:1000',
            ],
            'company_website' => [
                Rule::requiredIf(fn () => false),
                'nullable',
                'url',
                'max:255',
            ],
            'company_country' => [
                'nullable',
                'string',
                'max:100',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => __('talenma.auth.company_name'),
            'first_name' => __('talenma.auth.first_name'),
            'last_name' => __('talenma.auth.last_name'),
            'email' => __('talenma.auth.email'),
            'password' => __('talenma.auth.password'),
            'password_confirmation' => __('talenma.auth.confirm_password'),
            'role' => __('talenma.auth.register_as'),
            'sector' => __('talenma.auth.sector'),
            'description' => __('talenma.auth.registration_description'),
            'documents' => __('talenma.auth.registration_documents'),
            'representative_name' => __('talenma.auth.representative_name'),
            'representative_email' => __('talenma.auth.representative_email'),
            'company_need' => __('talenma.auth.company_need'),
            'company_website' => __('talenma.auth.company_website'),
            'company_country' => __('talenma.auth.company_country'),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => __('talenma.auth.validation.name_required'),
            'name.min' => __('talenma.auth.validation.name_min'),
            'name.max' => __('talenma.auth.validation.name_max'),
            'name.regex' => __('talenma.auth.validation.name_format'),
            'first_name.required' => __('talenma.auth.validation.first_name_required'),
            'first_name.min' => __('talenma.auth.validation.name_min'),
            'first_name.max' => __('talenma.auth.validation.name_max'),
            'first_name.regex' => __('talenma.auth.validation.name_format'),
            'last_name.required' => __('talenma.auth.validation.last_name_required'),
            'last_name.min' => __('talenma.auth.validation.name_min'),
            'last_name.max' => __('talenma.auth.validation.name_max'),
            'last_name.regex' => __('talenma.auth.validation.name_format'),
            'email.required' => __('talenma.auth.validation.email_required'),
            'email.email' => __('talenma.auth.validation.email_invalid'),
            'email.unique' => __('talenma.auth.validation.email_taken'),
            'email.max' => __('talenma.auth.validation.email_max'),
            'password.required' => __('talenma.auth.validation.password_required'),
            'password.confirmed' => __('talenma.auth.validation.password_confirmed'),
            'password.min' => __('talenma.auth.validation.password_min'),
            'password.letters' => __('talenma.auth.validation.password_letters'),
            'password.numbers' => __('talenma.auth.validation.password_numbers'),
            'password.max' => __('talenma.auth.validation.password_max'),
            'role.required' => __('talenma.auth.validation.role_required'),
            'role.in' => __('talenma.auth.validation.role_invalid'),
            'website.prohibited' => __('talenma.auth.validation.spam_detected'),
            'sector.required' => __('talenma.auth.validation.sector_required'),
            'sector.exists' => __('talenma.auth.validation.sector_invalid'),
            'description.required' => __('talenma.auth.validation.description_required'),
            'description.min' => __('talenma.auth.validation.description_min'),
            'description.max' => __('talenma.auth.validation.description_max'),
            'documents.required' => __('talenma.auth.validation.documents_required'),
            'documents.min' => __('talenma.auth.validation.documents_min'),
            'documents.max' => $this->input('role') === 'company'
                ? __('talenma.auth.validation.documents_max_company')
                : __('talenma.auth.validation.documents_max'),
            'documents.*.max' => __('talenma.auth.validation.documents_size'),
            'documents.*.mimes' => __('talenma.auth.validation.documents_type'),
            'representative_name.required' => __('talenma.auth.validation.representative_name_required'),
            'representative_name.min' => __('talenma.auth.validation.representative_name_min'),
            'representative_name.max' => __('talenma.auth.validation.representative_name_max'),
            'representative_name.regex' => __('talenma.auth.validation.representative_name_format'),
            'representative_email.required' => __('talenma.auth.validation.representative_email_required'),
            'representative_email.email' => __('talenma.auth.validation.representative_email_invalid'),
            'representative_email.max' => __('talenma.auth.validation.representative_email_max'),
            'company_need.required' => __('talenma.auth.validation.company_need_required'),
            'company_need.min' => __('talenma.auth.validation.company_need_min'),
            'company_need.max' => __('talenma.auth.validation.company_need_max'),
        ];
    }

    /**
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());
        $minutes = max(1, (int) ceil($seconds / 60));

        throw ValidationException::withMessages([
            'email' => __('talenma.auth.validation.too_many_attempts', ['minutes' => $minutes]),
        ]);
    }

    public function throttleKey(): string
    {
        return Str::transliterate('register|'.Str::lower($this->string('email')).'|'.$this->ip());
    }

    public function hitRateLimiter(): void
    {
        RateLimiter::hit($this->throttleKey(), 60);
    }

    public function clearRateLimiter(): void
    {
        RateLimiter::clear($this->throttleKey());
    }

    public function validateResolved(): void
    {
        $this->ensureIsNotRateLimited();

        parent::validateResolved();
    }

    protected function failedValidation(Validator $validator): void
    {
        $this->hitRateLimiter();

        parent::failedValidation($validator);
    }
}
