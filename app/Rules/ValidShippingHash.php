<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidShippingHash implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $found = app('App\Services\ShippingMethodService')->getShippingMethod($value);
        if (!$found) {
            $fail('Shipping method is invalid or has expired.');
        }
    }
}
