<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if ($this->user()?->isCompanyMember() || $this->user()?->isCompanyOwner()) {
            return;
        }

        $firstName = is_string($this->first_name)
            ? trim(preg_replace('/\s+/u', ' ', $this->first_name) ?? '')
            : $this->first_name;
        $lastName = is_string($this->last_name)
            ? trim(preg_replace('/\s+/u', ' ', $this->last_name) ?? '')
            : $this->last_name;

        $this->merge([
            'first_name' => $firstName,
            'last_name' => $lastName,
        ]);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_avatar' => ['nullable', 'boolean'],
        ];

        // Company members: identity (name + email) is managed by the company owner only.
        if ($this->user()?->isCompanyMember()) {
            return $rules;
        }

        $rules['email'] = [
            'required',
            'string',
            'lowercase',
            'email',
            'max:255',
            Rule::unique(User::class, 'email')->ignore($this->user()->id),
            Rule::unique(User::class, 'pending_email')->ignore($this->user()->id),
        ];

        if ($this->user()?->isCompanyOwner()) {
            $rules['name'] = ['required', 'string', 'max:255'];

            return $rules;
        }

        $personNameRules = [
            'required',
            'string',
            'min:2',
            'max:127',
            'regex:/^[\p{L}\p{M}][\p{L}\p{M}\s\'\-\.]*$/u',
        ];

        $rules['first_name'] = $personNameRules;
        $rules['last_name'] = $personNameRules;

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => __('talenma.account.name_company'),
            'first_name' => __('talenma.auth.first_name'),
            'last_name' => __('talenma.auth.last_name'),
            'email' => __('talenma.account.email'),
            'avatar' => __('talenma.account.avatar'),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'first_name.required' => __('talenma.auth.validation.first_name_required'),
            'last_name.required' => __('talenma.auth.validation.last_name_required'),
            'first_name.min' => __('talenma.auth.validation.first_name_min'),
            'last_name.min' => __('talenma.auth.validation.last_name_min'),
            'first_name.max' => __('talenma.auth.validation.first_name_max'),
            'last_name.max' => __('talenma.auth.validation.last_name_max'),
            'first_name.regex' => __('talenma.auth.validation.first_name_format'),
            'last_name.regex' => __('talenma.auth.validation.last_name_format'),
        ];
    }
}
