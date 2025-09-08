<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(120)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            Route::middleware(['api'])->prefix('api')->group(function() {
                require base_path('routes/admin/setup.php');
                require base_path('routes/admin/user.php');
                require base_path('routes/admin/cloud-provider.php');
                require base_path('routes/admin/payment.php');
                require base_path('routes/admin/server.php');
                require base_path('routes/admin/support.php');
                require base_path('routes/admin/dashboard.php');
                require base_path('routes/admin/webhook.php');
                
                require base_path('routes/user/account.php');
                require base_path('routes/user/cloud-provider.php');
                require base_path('routes/server/server.php');
                require base_path('routes/api.php');
                require base_path('/routes/user/payment.php');
                require base_path('/routes/user/support.php');
            });

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}
