<?php

namespace App\Observers;

use App\Models\Server\Server;
use App\Services\WebhookService;
use App\Models\Admin\Webhook\WebhookLog;

class ServerObserver
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
     * Handle the Server "created" event.
     */
    public function created(Server $server): void
    {
        //
    }

    /**
     * Handle the Server "updated" event.
     */
    public function updated(Server $server): void
    {
        $user = $server->user;
        if($server->agent_status == 'finalizing') {
            $this->webhookService->send('Server', 'Created', [
                'id'    => $server->id,
                'user_id' => $user?->id,
                'user_name' => $user?->name,
                'user_email' => $user?->email,
                'name'  => $server->name,
                'ip' => $server->ip,
                'provider' => $server->provider_name,
                'web_server' => $server->web_server,
                'version' => $server->version,
                'created_at' => $server->created_at,
            ]);
        }
    }

    /**
     * Handle the Server "deleted" event.
     */
    public function deleted(Server $server): void
    {
        $user = $server->user;
        $this->webhookService->send('Server', 'Deleted', [
            'id'    => $server->id,
            'user_id' => $user?->id,
            'user_name' => $user?->name,
            'user_email' => $user?->email,
            'name'  => $server->name,
            'ip' => $server->ip,
            'provider' => $server->provider_name,
            'web_server' => $server->web_server,
            'version' => $server->version,
            'deleted_at' => $server->deleted_at,
        ]);
    }

    /**
     * Handle the Server "restored" event.
     */
    public function restored(Server $server): void
    {
        //
    }

    /**
     * Handle the Server "force deleted" event.
     */
    public function forceDeleted(Server $server): void
    {
        //
    }
}
