<?php

namespace App\Console\Commands\User;

use App\Enums\UserStatus as UserStatusEnum;
use App\Jobs\User\AutoRechargeWallet;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ExecuteAutoRecharge extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'execute:auto-recharge';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Queue auto recharge for users below configured credit threshold.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $queuedUsers = 0;
            $nextDelayInSeconds = 5;

            User::where('auto_recharge_enabled', true)
                ->whereNotNull('auto_recharge_minimum_credit')
                ->whereNotNull('auto_recharge_amount')
                ->whereNotNull('auto_recharge_payment_gateway')
                ->whereColumn('credits', '<=', 'auto_recharge_minimum_credit')
                ->chunk(10, function ($users) use (&$queuedUsers, &$nextDelayInSeconds) {
                    foreach ($users as $user) {
                        AutoRechargeWallet::dispatch($user)
                            ->onQueue('default')
                            ->delay(now()->addSeconds($nextDelayInSeconds));

                        $queuedUsers++;
                        $nextDelayInSeconds += 5;
                    }
                });

            $this->info("Auto recharge queued successfully for {$queuedUsers} user(s).");
            Log::info('Auto recharge users queued.', [
                'queued_users' => $queuedUsers,
                'last_delay_seconds' => $queuedUsers > 0 ? $nextDelayInSeconds - 5 : 0,
            ]);
        } catch (\Exception $e) {
            report($e);
            $this->error('An error occurred while dispatching auto recharge.');
            Log::error('Auto recharge dispatch command failed.', [
                'message' => $e->getMessage(),
            ]);
        }
    }
}
