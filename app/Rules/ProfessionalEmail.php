<?php

namespace App\Rules;

use App\Support\ConsumerEmail;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ProfessionalEmail implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || ConsumerEmail::isPersonal($value)) {
            $fail(__('talenma.company_offer.trial_email_professional'));
        }
    }
}
