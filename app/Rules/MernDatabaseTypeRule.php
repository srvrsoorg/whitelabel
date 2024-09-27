<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class MernDatabaseTypeRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
         $webServer = request()->input('web_server');

        // Check if MongoDB is selected only when the web server is 'mern'
        if ($webServer === 'mern' && $value !== 'mongodb') {
            $fail('MongoDB is required when using the MERN stack.');
        }

        // If web server is not 'mern' and MongoDB is selected, it's not valid
        if ($webServer !== 'mern' && $value === 'mongodb') {
            $fail('MongoDB is not supported with the selected web server.');
        }
    }
}
