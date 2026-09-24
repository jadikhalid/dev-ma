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
        $firstName = is_string($this->first_name)
            ? trim(preg_replace('/\s+/u', ' ', $this->first_name) ?? '')
            : $this->first_name;
        $lastName = is_string($this->last_name)
            ? trim(preg_replace('/\s+/u', ' ', $this->last_name) ?? '')
            : $this->last_name;

        $representativeName = null;
        if (
            is_string($firstName)
            && is_string($lastName)
            && $firstName !== ''
            && $lastName !== ''
        ) {
            $representativeName = trim($firstName.' '.$lastName);
        }

        $description = is_string($this->description) ? trim($this->description) : $this->description;

        $this->merge([
            'name' => is_string($this->name) ? trim(preg_replace('/\s+/u', ' ', $this->name) ?? '') : $this->name,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'contact_name' => (is_string($representativeName) && $representativeName !== '') ? $representativeName : null,
            'representative_name' => (is_string($representativeName) && $representativeName !== '') ? $representativeName : null,
            'email' => is_string($this->email) ? Str::lower(trim($this->email)) : $this->email,
            'description' => $description === '' ? null : $description,
        ]);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'nullable',
                'string',
                'min:2',
                'max:255',
                'regex:/^[\p{L}\p{M}][\p{L}\p{M}\s\'\-\.]*$/u',
            ],
            'first_name' => [
                'required',
                'string',
                'min:2',
                'max:127',
                'regex:/^[\p{L}\p{M}][\p{L}\p{M}\s\'\-\.]*$/u',
            ],
            'last_name' => [
                'required',
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
            'role' => ['required', 'string', 'in:dev'],
            'website' => ['prohibited'],
            'sector' => [
                'nullable',
                'string',
                'max:64',
                Rule::exists('profession_sectors', 'slug')->where(fn ($query) => $query->where('is_active', true)),
            ],
            // Launch phase: talent profile description is optional / hidden on the form.
            'description' => [
                'nullable',
                'string',
                'max:2550',
            ],
            'cv' => [
                'nullable',
                'file',
                'max:1024',
                'mimes:pdf,jpg,jpeg,png,webp',
            ],
            'cv_language' => [
                'nullable',
                'string',
                Rule::in(\App\Models\ProfileDocument::CV_LANGUAGES),
            ],
            'data_processing_consent' => ['accepted'],
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
            'cv' => __('talenma.talent.cv'),
            'cv_language' => __('talenma.talent.cv_language'),
            'data_processing_consent' => __('talenma.auth.data_processing_consent_label'),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.min' => __('talenma.auth.validation.name_min'),
            'name.max' => __('talenma.auth.validation.name_max'),
            'name.regex' => __('talenma.auth.validation.name_format'),
            'first_name.required' => __('talenma.auth.validation.first_name_required'),
            'first_name.min' => __('talenma.auth.validation.first_name_min'),
            'first_name.max' => __('talenma.auth.validation.first_name_max'),
            'first_name.regex' => __('talenma.auth.validation.first_name_format'),
            'last_name.required' => __('talenma.auth.validation.last_name_required'),
            'last_name.min' => __('talenma.auth.validation.last_name_min'),
            'last_name.max' => __('talenma.auth.validation.last_name_max'),
            'last_name.regex' => __('talenma.auth.validation.last_name_format'),
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
            'sector.exists' => __('talenma.auth.validation.sector_invalid'),
            'description.max' => __('talenma.auth.validation.description_max'),
            'cv.max' => __('talenma.auth.validation.documents_size'),
            'cv.mimes' => __('talenma.auth.validation.documents_type'),
            'cv_language.in' => __('talenma.talent.cv_language_invalid'),
            'data_processing_consent.required' => __('talenma.auth.validation.data_processing_consent_required'),
            'data_processing_consent.accepted' => __('talenma.auth.validation.data_processing_consent_required'),
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
