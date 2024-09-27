<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class AmountFormatRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!preg_match('/^\d{1,7}(\.\d{0,3})?$/', $value)) {
            $fail('Enter up to 7 digits before and 3 digits after the decimal point (e.g., 1234567.123).');
        }
    }
}
