<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class MernNodejsRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Check Node.js requirement only if the web server is set to 'mern'
        if (request()->input('web_server') === 'mern' && !(bool) $value) {
            $fail('Node.js is required for installing MERN stack.');
        }
    }
}
