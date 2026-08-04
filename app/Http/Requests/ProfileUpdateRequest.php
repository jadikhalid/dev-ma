<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
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
        $rules['name'] = ['required', 'string', 'max:255'];

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => __('talenma.account.name'),
            'email' => __('talenma.account.email'),
            'avatar' => __('talenma.account.avatar'),
        ];
    }
}
