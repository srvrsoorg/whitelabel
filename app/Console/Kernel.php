<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('telescope:prune --hours=24')->daily();
        $schedule->command('horizon:snapshot')->everyFiveMinutes();

        // Subscription
        $schedule->command('billing:execute-hourly-subscriptions')->everyMinute();
        $schedule->command('execute-credit-reminder')->daily();

        // User Wallet Reminder
        $schedule->command('execute:wallet-reminder')->daily();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
