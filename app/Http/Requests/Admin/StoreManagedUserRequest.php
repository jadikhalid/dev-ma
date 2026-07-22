<?php

namespace App\Http\Requests\Admin;

use App\Models\CompanyProfile;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreManagedUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isStaff() ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => ['required', 'string', 'in:dev,company'],
            'approve_immediately' => ['sometimes', 'boolean'],
            'email_verified' => ['sometimes', 'boolean'],
            'country' => ['nullable', 'string', Rule::in(CompanyProfile::COUNTRY_CODES)],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function validatedPayload(): array
    {
        $validated = $this->validated();

        return [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => $validated['role'],
            'approve_immediately' => (bool) ($validated['approve_immediately'] ?? false),
            'email_verified' => (bool) ($validated['email_verified'] ?? true),
            'country' => $validated['country'] ?? CompanyProfile::DEFAULT_COUNTRY,
        ];
    }
}
