<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Http\Utilities\Client;
use App\Models\Admin\SiteSetting;

class ServerNameRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if(auth()->user()->servers()->where('name', $value)->exists()){
            $fail('The :attribute already exists.');
        }
        
        $organizationId = SiteSetting::value('sa_org_id');
        $validateServerName = Client::serveravatar("self-hosted/organizations/{$organizationId}/validate-server-name", 'POST', ['name' => $value]);
        if(isset($validateServerName['error'])) {
            $fail($validateServerName['message']);
        }
    }
}
