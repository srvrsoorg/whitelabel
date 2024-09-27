<?php

namespace App\Jobs\Subscription;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\Admin\BillingDetail;
use App\Mail\User\CreditReminder\CreditReminder as CreditReminderMail;
use App\Mail\User\CreditReminder\{SecondCreditReminderMail, ServerDeletionMail};
use Carbon\Carbon;
use App\Http\Helper;

class CreditReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $user;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $user = $this->user;
            $creditReminders = $user->creditReminders()->whereHas('server')->get();
            $billingDetail = BillingDetail::first();
            $today = today();
            $hasDueMailSentToday = false;
            $hasRetentionMailSentToday = false;
            $hasEndMailSentToday = false;

            // Get all credit reminders for the user
            foreach ($creditReminders as $reminder) {
                $start = Carbon::parse($reminder->started_at)->format('Y-m-d');
                $due = Carbon::parse($reminder->due_at)->format('Y-m-d');
                $end = Carbon::parse($reminder->end_at)->format('Y-m-d');
                
                // Send Mails in Due days Period
                if ($today->greaterThanOrEqualTo($start) && $today->lessThan($due)) {
                    // Calculate the number of days since the start date
                    $daysSinceStart = $today->diffInDays($start);
                    
                    // Check if the number of days is odd
                    if ($daysSinceStart % 2 == 0 || $daysSinceStart == 0) {
                        if (!$hasDueMailSentToday) {
                            \Mail::to($user)->queue((new CreditReminderMail($user))->onQueue('subscription'));

                            // Mark that an email has been sent today
                            $hasDueMailSentToday = true;
                        }
                    }
                }

                // Send Mail in Retention Period
                if ($today->greaterThanOrEqualTo($due) && $today->lessThan($end)) {
                    // Calculate the number of days since the due date
                    $daysSinceDue = $today->diffInDays($due);

                    if ($daysSinceDue % 2 == 0 || $daysSinceDue == 0) {
                        if (!$hasRetentionMailSentToday) {
                            \Mail::to($user)->queue((new SecondCreditReminderMail($user))->onQueue('subscription'));
                            // Mark that an email has been sent today
                            $hasRetentionMailSentToday = true;
                        }
                    }
                }

                // Send Mail on End date & Delete Server
                $creditReminders = $user->creditReminders()
                    ->whereHas('server')
                    ->whereDate('end_at', today())
                    ->get();

                if ($creditReminders->isNotEmpty()) {
                    if(!$hasEndMailSentToday) {
                        // Collect server data
                        $serverData = $creditReminders->map(function ($creditReminder) {
                            $server = $creditReminder->server;
                            return [
                                'name' => $server->name,
                                'ip' => $server->ip,
                            ];
                        })->toArray();

                        // Send a single email with all reminders
                        \Mail::to($user)->queue((new ServerDeletionMail($user, $serverData))->onQueue('subscription'));

                        $hasEndMailSentToday = true;
                    }

                    foreach ($creditReminders as $creditReminder) {
                       Helper::handleServerDeletion($creditReminder->server, false);
                    }
                }
            }
        } catch (\Exception $e) {
            report($e);
            $this->fail("Error occur while Sending Email");
        }
    }
}