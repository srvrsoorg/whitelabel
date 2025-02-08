<?php

namespace App\Console\Commands\Subscription;

use Illuminate\Console\Command;
use App\Models\Server\Subscription;
use App\Jobs\Subscription\DeductHourlyCharges;

class ExecuteHourlySubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'billing:execute-hourly-subscriptions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Execute server subscription for Deduction of hourly subscription';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {

            // Calculate the timestamp for one hour ago
            $oneHourAgo = now()->subHour();

            // Query subscriptions of type 'hourly' that are associated with a server
            // and either have no usage summaries or need to be updated
            Subscription::where('type', 'hourly')
                ->whereHas('server')
                ->where(function ($query) use ($oneHourAgo) {
                    $query->whereDoesntHave('usageSummaries')
                          ->orWhereHas('latestUsageSummary', function ($query) use ($oneHourAgo) {
                              $query->whereNull('last_deduct_at')
                                    ->orWhere('last_deduct_at', '<=', $oneHourAgo);
                          });
                })
                ->chunk(100, function ($subscriptions) {
                    foreach ($subscriptions as $subscription) {
                        // Dispatch the job to deduct hourly charges for each subscription
                        DeductHourlyCharges::dispatch($subscription)->onQueue('subscription')->delay(now()->addSeconds(30));
                    }
                });
        } catch (\Exception $e) {
            // Report the exception
            report($e);

             // Optional: Log or notify about the failure
            $this->error('An error occurred while sending subscriptions.');
        }

    }
}
