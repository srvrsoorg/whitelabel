<?php

namespace App\Helpers;

use Illuminate\Support\Facades\File;

class Locale
{
    public static function get($countryCode): string
    {
        try{
            $locales = File::json(config_path("locales.json"));

            $country = collect($locales)->first(function ($locale) use ($countryCode) {
                return $locale['country']['code'] === $countryCode;
            });

            return $country['locale'];
        }catch(\Exception $e){
            report($e);
            return 'en-US';
        }
    }
}