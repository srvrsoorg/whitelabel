<?php

namespace App\Observers;

use App\Models\Ticket;
use App\Services\WebhookService;

class TicketObserver
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
     * Handle the Ticket "created" event.
     */
    public function created(Ticket $ticket): void
    {
        $user = $ticket->user;
        $this->webhookService->send('Ticket', 'Created', [
            'user' => [
                'user_id' => $ticket->user_id,
                'user_name' => $user?->name,
                'user_email' => $user?->email,
            ],
            'ticket' => [
                'id' => $ticket->id,
                'title' => $ticket->title,
                'department' => $ticket->department,
                'status' => 'open',
                'created_at' => $ticket->created_at
            ]
        ]);
    }

    /**
     * Handle the Ticket "updated" event.
     */
    public function updated(Ticket $ticket): void
    {
        $user = $ticket->user;
        if ($ticket->wasChanged('status')) {
            $action = $ticket->status == 'open' ? 'Reopened' : 'Closed';

            $this->webhookService->send('Ticket', $action, [
                'user' => [
                    'user_id'    => $ticket->user_id,
                    'user_name' => $user?->name,
                    'user_email' => $user?->email,
                ],
                'ticket' => [
                    'id'         => $ticket->id,
                    'title'      => $ticket->title,
                    'department' => $ticket->department,
                    'status'     => $ticket->status,
                    'updated_at' => $ticket->updated_at,
                ]
            ]);
        }
    }

    /**
     * Handle the Ticket "deleted" event.
     */
    public function deleted(Ticket $ticket): void
    {
        //
    }

    /**
     * Handle the Ticket "restored" event.
     */
    public function restored(Ticket $ticket): void
    {
        //
    }

    /**
     * Handle the Ticket "force deleted" event.
     */
    public function forceDeleted(Ticket $ticket): void
    {
        //
    }
}
