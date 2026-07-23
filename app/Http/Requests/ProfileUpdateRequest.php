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
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_avatar' => ['nullable', 'boolean'],
        ];

        if ($this->user()?->isCompanyMember()) {
            $rules['first_name'] = ['required', 'string', 'min:2', 'max:127'];
            $rules['last_name'] = ['required', 'string', 'min:2', 'max:127'];
        } else {
            $rules['name'] = ['required', 'string', 'max:255'];
        }

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => __('talenma.account.name'),
            'first_name' => __('talenma.auth.first_name'),
            'last_name' => __('talenma.auth.last_name'),
            'email' => __('talenma.account.email'),
            'avatar' => __('talenma.account.avatar'),
        ];
    }
}
