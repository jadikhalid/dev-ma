<?php

namespace App\Http\Requests\Auth;

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
            'email' => is_string($this->email) ? Str::lower(trim($this->email)) : $this->email,
        ]);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:2',
                'max:255',
                'regex:/^[\p{L}\p{M}][\p{L}\p{M}\s\'\-\.]*$/u',
            ],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                'unique:'.User::class,
            ],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string', 'in:dev,company'],
            'website' => ['prohibited'],
            'sector' => [
                Rule::requiredIf(fn () => $this->input('role') === 'dev'),
                'nullable',
                'string',
                'max:64',
                Rule::exists('profession_sectors', 'slug')->where(fn ($query) => $query->where('is_active', true)),
            ],
            'description' => [
                Rule::requiredIf(fn () => $this->input('role') === 'dev'),
                'nullable',
                'string',
                'min:20',
                'max:500',
            ],
            'documents' => [
                Rule::requiredIf(fn () => $this->input('role') === 'dev'),
                'nullable',
                'array',
                'min:1',
                'max:3',
            ],
            'documents.*' => [
                'file',
                'max:1024',
                'mimes:pdf,jpg,jpeg,png,webp',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => __('talenma.auth.full_name'),
            'email' => __('talenma.auth.email'),
            'password' => __('talenma.auth.password'),
            'password_confirmation' => __('talenma.auth.confirm_password'),
            'role' => __('talenma.auth.register_as'),
            'sector' => __('talenma.auth.sector'),
            'description' => __('talenma.auth.registration_description'),
            'documents' => __('talenma.auth.registration_documents'),
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
            'documents.max' => __('talenma.auth.validation.documents_max'),
            'documents.*.max' => __('talenma.auth.validation.documents_size'),
            'documents.*.mimes' => __('talenma.auth.validation.documents_type'),
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
