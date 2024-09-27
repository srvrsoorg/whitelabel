<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Interfaces\{ActivityRepositoryInterface, TwoFaRepositoryInterface, TicketRepositoryInterface};
use App\Repositories\{ActivityRepository, TwoFaRepository, TicketRepository};
use App\Interfaces\Server\ServerRepositoryInterface;
use App\Repositories\Server\ServerRepository;
use App\Interfaces\UsageSummary\UsageSummaryRepositoryInterface;
use App\Repositories\UsageSummary\UsageSummaryRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(ActivityRepositoryInterface::class, ActivityRepository::class);
        $this->app->bind(TwoFaRepositoryInterface::class, TwoFaRepository::class);
        $this->app->bind(ServerRepositoryInterface::class, ServerRepository::class);
        $this->app->bind(TicketRepositoryInterface::class, TicketRepository::class);
        $this->app->bind(UsageSummaryRepositoryInterface::class, UsageSummaryRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
