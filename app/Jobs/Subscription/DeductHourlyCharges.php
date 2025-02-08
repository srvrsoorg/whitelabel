<?php

namespace App\Jobs\Subscription;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Server\Subscription;
use App\Models\Admin\BillingDetail;
use \Carbon\Carbon;

class DeductHourlyCharges implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $subscription;

    /**
     * Create a new job instance.
     */
    public function __construct(Subscription $subscription)
    {
        $this->subscription = $subscription; 
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $subscription = $this->subscription;
            $server = $subscription->server;
            if(!$server) {
                $this->fail("Server not found.");
            }
            
            $user = $subscription->user;
            $billingDetail = BillingDetail::first();

            // Store User Credit Reminder 
            if ($user->credits <= 0 && !$subscription->creditReminder) {
                if($billingDetail && $billingDetail->billing_mode == 'prepaid'){
                    // Create a new record if no existing reminder
                    $creditReminder = $user->creditReminders()->create([
                        'server_id' => $server->id,
                        'subscription_id' => $subscription->id,
                        'started_at' => now(),
                        'due_at' => now()->addDays($billingDetail->due_days),
                        'end_at' => now()->addDays($billingDetail->due_days + $billingDetail->retention_period),
                    ]);
                } elseif ($billingDetail && $billingDetail->billing_mode == 'postpaid' && now()->isSameDay(now()->startOfMonth())){
                   $creditReminder = $user->creditReminders()->create([
                        'server_id' => $server->id,
                        'subscription_id' => $subscription->id,
                        'started_at' => now(),
                        'due_at' => now()->addDays($billingDetail->due_days),
                        'end_at' => now()->addDays($billingDetail->due_days + $billingDetail->retention_period),
                    ]);
                }
            }elseif($user->credits > 0 && $subscription->creditReminder) {
                // Delete existing reminders if user credits are positive
                $user->creditReminders()->where('subscription_id', $subscription->id)->delete();
            }

            // Check if there's already an entry for this month
            $currentMonth = now()->startOfMonth()->format('Y-m-d'); // First day of the current month
            $activeCharges = $subscription->usageSummaries()->firstOrCreate([
                'user_id' => $user->id,
                'server_id' => $server->id,
                'subscription_id' => $subscription->id, 
                'started_at' => $currentMonth, // Ensure this is for the current month
            ],[
                'deduct_amount' => 0,
                'server_name' => $server->name,
                'server_ip' => $server->ip,
            ]);
            
            $hourlyRate = $subscription->monthly_price / (now()->daysInMonth * 24);

            // Default to 1 hour ago if not set
            $lastDeductAt = $activeCharges->last_deduct_at ? $activeCharges->last_deduct_at : now()->subHour();
            $hoursDiff = now()->diffInHours($lastDeductAt);

            if(!$activeCharges->last_deduct_at) {
                $activeCharges->created_at = $lastDeductAt;
                $activeCharges->last_deduct_at = $lastDeductAt;
                $activeCharges->save();
            }

            if ($hoursDiff > 0) {
                $totalDeduction = $hoursDiff * $hourlyRate;
                $subscription->user->decrement('credits', $totalDeduction);
                $activeCharges->increment('deduct_amount', $totalDeduction);
                $activeCharges->update(['last_deduct_at' => now()->parse($activeCharges->last_deduct_at)->addHours($hoursDiff)->toDateTimeString()]);
                $activeCharges->update(['started_at' => $currentMonth]);
            }

        } catch (\Exception $e) {
            report($e);
            $this->fail("Something went wrong while deduct hourly charges of subscription : " . $subscription->id);
        }

    }
}
