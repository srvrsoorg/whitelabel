<?php

namespace App\Helpers;

use Illuminate\Support\Facades\File;

class Currency
{
    public static function all()
    {
        $currency = File::json(config_path("currencies.json"));
        return $currency;
    }

    public static function findDecimalDigits($currency)
    {
        try{
            $currencies = File::json(config_path("currencies.json"));
            return $currencies[$currency]['decimal_digits'];
        }catch (\Exception $e){
            report($e);
            return 0;
        }
    }

    public static function formatAmount($amount, $currency)
    {
        $decimal_digit = self::findDecimalDigits($currency);

        // Ensure the correct number of decimal places, even if they are zeros
        return number_format((float)$amount, $decimal_digit, '.', '');
    }
}