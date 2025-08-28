<?php

namespace App\Jobs\User;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Http\Helper;
use App\Mail\User\SendWalletReminder;
use Mail;

class WalletReminder implements ShouldQueue
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
     * This method handles sending the credit reminder email
     * if the user's available credits fall below their set minimum threshold.
     */
    public function handle(): void
    {
        try {
            $user = $this->user;
            $dayOfWeek = now()->dayOfWeek;
            $goodDayToMail = app()->isProduction() ? in_array($dayOfWeek, [1, 3, 5]) : true;
            
            $availableCredits = $user->credits;

            // Send reminder if credits are below threshold AND it's a good day to mail
            if($availableCredits < $user->reminder_minimum_credit && $goodDayToMail) {
                \Mail::to($user)->queue((new SendWalletReminder($user, $availableCredits))->onQueue('default'));
            }
        } catch (\Exception $e) {
            report($e);
            $this->fail("Something went wrong while sending minimum credit reminder for user ({$this->user->id}) : " . $e->getMessage());
        }
    }
}
