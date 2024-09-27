<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\CashierServiceProvider as BaseServiceProvider;

class CashierServiceProvider extends ServiceProvider
{
    /**
     * The migrations that should be ignored.
     *
     * @var array
     */
    protected $ignoreMigrations = [
        'create_customer_columns',
        'create_subscriptions_table',
        'create_payment_methods_table',
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
