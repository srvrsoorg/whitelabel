<?php

namespace App\Observers;

use App\Models\Billing\Transaction;
use App\Services\WebhookService;

class TransactionObserver
{
    protected $webhookService;

    /**
     * Create a new observer instance.
     *
     * @param  \App\Services\WebhookService  $webhookService
     * @return void
     */
    public function __construct(WebhookService $webhookService)
    {
        $this->webhookService = $webhookService;
    }

    /**
     * Handle the Transaction "created" event.
     */
    public function created(Transaction $transaction): void
    {
       $this->webhookService->send('Transaction', 'Created', $this->buildPayload($transaction));
    }

    /**
     * Handle the Transaction "updated" event.
     */
    public function updated(Transaction $transaction): void
    {
       if ($transaction->wasChanged('status')) {
            $action = $this->mapStatus($transaction->status);

            if ($action != 'unknown') {
                $this->webhookService->send('Transaction', ucfirst($action), $this->buildPayload($transaction));
            }
        }
    }

    /**
     * Handle the Transaction "deleted" event.
     */
    public function deleted(Transaction $transaction): void
    {
        //
    }

    /**
     * Handle the Transaction "restored" event.
     */
    public function restored(Transaction $transaction): void
    {
        //
    }

    /**
     * Handle the Transaction "force deleted" event.
     */
    public function forceDeleted(Transaction $transaction): void
    {
        //
    }

    protected function mapStatus(?int $status): string
    {
        return match ($status) {
            1 => 'success',
            0 => 'failed',
            2 => 'pending',
            3 => 'refunded',
            default => 'unknown',
        };
    }

    protected function buildPayload(Transaction $transaction): array
    {
        $user = $transaction->user;
        return [
            'id'             => $transaction->id,
            'transaction_id' => $transaction->transaction_id,
            'payment_gateway'=> $transaction->payment_gateway,
            'service'        => $transaction->service,
            'amounts' => [
                'base'     => $transaction->base_amount,
                'discount' => $transaction->discount_amount,
                'tax'      => $transaction->tax_amount,
                'final'    => $transaction->final_amount,
            ],
            'status'         => $this->mapStatus($transaction->status),
            'paid_at'        => $transaction->paid_at,
            'refunded_at'    => $transaction->refunded_at,
            'refund_reason'  => $transaction->refund_reason,
            'user_id'        => $transaction->user_id,
            'user_name'      => $user?->name,
            'user_email'     => $user?->email,
        ];
    }
}
