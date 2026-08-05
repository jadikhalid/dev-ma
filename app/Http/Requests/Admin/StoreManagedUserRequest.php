<?php

namespace App\Http\Requests\Admin;

use App\Models\CompanyProfile;
use App\Models\PendingRegistration;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreManagedUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'company_name' => is_string($this->company_name)
                ? trim(preg_replace('/\s+/u', ' ', $this->company_name) ?? '')
                : $this->company_name,
            'first_name' => is_string($this->first_name)
                ? trim(preg_replace('/\s+/u', ' ', $this->first_name) ?? '')
                : $this->first_name,
            'last_name' => is_string($this->last_name)
                ? trim(preg_replace('/\s+/u', ' ', $this->last_name) ?? '')
                : $this->last_name,
            'email' => is_string($this->email) ? Str::lower(trim($this->email)) : $this->email,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'company_name' => [
                Rule::requiredIf(fn () => $this->input('role') === 'company'),
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
                Rule::unique(User::class, 'email'),
                Rule::unique(User::class, 'pending_email'),
                Rule::unique(PendingRegistration::class, 'email'),
            ],
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => ['required', 'string', 'in:dev,company'],
            'email_verified' => ['sometimes', 'boolean'],
            'country' => ['nullable', 'string', Rule::in(CompanyProfile::COUNTRY_CODES)],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        $isCompany = $this->input('role') === 'company';

        return [
            'company_name' => __('talenma.auth.company_name'),
            'first_name' => $isCompany
                ? __('talenma.auth.representative_first_name')
                : __('talenma.auth.first_name'),
            'last_name' => $isCompany
                ? __('talenma.auth.representative_last_name')
                : __('talenma.auth.last_name'),
            'email' => __('talenma.auth.email'),
            'password' => __('talenma.auth.password'),
            'role' => __('talenma.admin.users.role'),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        $isCompany = $this->input('role') === 'company';

        return [
            'company_name.required' => __('talenma.auth.validation.name_required'),
            'company_name.min' => __('talenma.auth.validation.name_min'),
            'company_name.max' => __('talenma.auth.validation.name_max'),
            'company_name.regex' => __('talenma.auth.validation.name_format'),
            'first_name.required' => $isCompany
                ? __('talenma.auth.validation.representative_first_name_required')
                : __('talenma.auth.validation.first_name_required'),
            'last_name.required' => $isCompany
                ? __('talenma.auth.validation.representative_last_name_required')
                : __('talenma.auth.validation.last_name_required'),
            'first_name.min' => __('talenma.auth.validation.first_name_min'),
            'last_name.min' => __('talenma.auth.validation.last_name_min'),
            'first_name.max' => __('talenma.auth.validation.first_name_max'),
            'last_name.max' => __('talenma.auth.validation.last_name_max'),
            'first_name.regex' => __('talenma.auth.validation.first_name_format'),
            'last_name.regex' => __('talenma.auth.validation.last_name_format'),
            'email.unique' => __('talenma.auth.validation.email_taken'),
            'email.email' => __('talenma.auth.validation.email_invalid'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function validatedPayload(): array
    {
        $validated = $this->validated();
        $firstName = $validated['first_name'];
        $lastName = $validated['last_name'];
        $personName = trim($firstName.' '.$lastName);
        $isCompany = $validated['role'] === 'company';

        return [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'name' => $isCompany
                ? trim((string) ($validated['company_name'] ?? ''))
                : $personName,
            'representative_name' => $isCompany ? $personName : null,
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => $validated['role'],
            'approve_immediately' => true,
            'email_verified' => (bool) ($validated['email_verified'] ?? true),
            'country' => $validated['country'] ?? null,
        ];
    }
}
