<?php

namespace App\Console\Commands\User;

use Illuminate\Console\Command;
use App\Jobs\User\CreditReminder as DispatchCreditReminder;
use App\Models\User;

class CreditReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'execute:credit-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send credit reminder if user credit is less than the set minimum reminder.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            // Fetch users who have set a minimum credit reminder and at least one server.
            User::whereNotNull('reminder_minimum_credit')
                ->whereHas('servers')
                ->chunk(10, function($users) {
                    foreach ($users as $user) {
                        DispatchCreditReminder::dispatch($user)->onQueue('default')->delay(5);
                    }
                });

            $this->info('Users have been queued for credit reminder emails.');
        } catch (\Exception $e) {
            // Report the exception
            report($e);
            $this->error('An error occurred while dispatching credit reminders.');
        }
    }
}
