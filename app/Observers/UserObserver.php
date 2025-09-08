<?php

namespace App\Observers;

use App\Models\User;
use App\Services\WebhookService;

class UserObserver
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
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        $this->webhookService->send('User', 'Created', [
            'id'    => $user->id,
            'name'  => $user->name,
            'email' => $user->email,
            'country_name' => $user->country_name,
            'region_name' => $user->region_name,
            'created_at' => $user->created_at,
        ]);
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        $this->webhookService->send('User', 'Deleted', [
            'id'    => $user->id,
            'name'  => $user->name,
            'email' => $user->email,
            'country_name' => $user->country_name,
            'region_name' => $user->region_name,
            'deleted_at' => $user->deleted_at,
        ]);
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
