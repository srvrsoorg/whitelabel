<?php

namespace App\Jobs\Admin\Webhook;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class SendWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $webhook;
    public $event;
    public array $payload;

    /**
     * Create a new job instance.
     *
     * @param  \App\Models\Admin\Webhook\Webhook  $webhook
     * @param  \App\Models\Admin\Webhook\WebhookEvent  $event
     * @param  array  $payload
     */
    public function __construct($webhook, $event, array $payload)
    {
        $this->webhook = $webhook;
        $this->event   = $event;
        $this->payload = $payload;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Send POST request to the webhook URL
            $response = Http::post($this->webhook->url, $this->payload);

            // Store webhook log on success/failure response
            $this->webhook->logs()->create([
                'webhook_event_id' => $this->event?->id,
                'payload'          => $this->payload,
                'response_code'    => $response->status(),
                'response_body'    => $response->body(),
                'status'           => $response->successful() ? 'success' : 'failed',
                'delivered_at'     => now(),
            ]);
        } catch (\Exception $e) {
            // Store webhook log if the request completely fails
            $this->webhook->logs()->create([
                'webhook_event_id' => $this->event?->id,
                'payload'          => $this->payload,
                'response_code'    => 500,
                'response_body'    => $e->getMessage(),
                'status'           => 'failed',
                'delivered_at'     => now(),
            ]);
        }
    }
}
