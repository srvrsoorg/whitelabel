<?php

namespace App\Console\Commands\Subscription;

use Illuminate\Console\Command;
use App\Models\User;
use App\Jobs\Subscription\CreditReminder;

class ExecuteCreditReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'execute-credit-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Execute Credit Reminder and Send Mail to User.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            // Retrieve users with credits less than or equal to 0 and who have credit reminders
            User::where('credits', '<=', 0)
                ->whereHas('creditReminders')
                ->chunk(100, function ($users) {
                    foreach ($users as $user) {
                        // Dispatch the job to send a credit reminder email
                        CreditReminder::dispatch($user)
                            ->onQueue('subscription')
                            ->delay(now()->addSeconds(30));
                    }
                });
        } catch (\Exception $e) {
            // Report the exception
            report($e);

            // Optional: Log or notify about the failure
            $this->error('An error occurred while sending credit reminders.');
        }
    }
}
