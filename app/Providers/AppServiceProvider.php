<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Admin\Smtp;
use App\Http\Helper;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Number;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        try {

            // Set SMTP Detail
            $smtp = cache()->rememberForever(Smtp::CACHE_KEYS['main'], function() {
                return Smtp::latest()->first();
            });

            if ($smtp) {
                $smtp = Smtp::latest()->first();
                config()->set('mail', array_merge(config('mail'), [
                    'driver' => 'smtp',
                    'host' => $smtp->host,
                    'port' => $smtp->port,
                    'encryption' => $smtp->encryption,
                    'username' => $smtp->username,
                    'password' => $smtp->password,
                    'from' => [
                        'address' => $smtp->from_email,
                        'name' => $smtp->from_name
                    ]
                ]));
            }

        } catch (\Exception $e) {
            // Report any exceptions that occur during the configuration setup
            //report($e);
        }

        $siteSetting = Helper::siteSetting();
        if($siteSetting) {
            view()->share('logo', $siteSetting->icon);
            view()->share('currencySymbol', $siteSetting->currency_symbol);
            config()->set("currency.default", $siteSetting->currency);
            config()->set("currency.symbol", $siteSetting->currency_symbol);
            config()->set("currency.locale", $siteSetting->locale);
        } else {
            view()->share('logo',url('logo/logo-sm.png'));
            view()->share('currencySymbol', '$');
        }

        UploadedFile::macro('getUuidName', function () {
            $extension = $this->getClientOriginalExtension();
            $fileName = str()->uuid().'.'.$extension;
            return $fileName;
        });

        Blade::directive('formatCurrency', function ($amount) {
            return "<?php echo \Illuminate\Support\Number::currency(floatval($amount), config('currency.default'), config('currency.locale')); ?>";
        });
    }
}
