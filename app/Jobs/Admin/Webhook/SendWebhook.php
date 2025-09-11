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
        $headers = [];

        try {
            $headers = $this->getHeaders();

            // Send POST request to the webhook URL
            $response = Http::withHeaders($headers)->post($this->webhook->url, $this->payload);

            // Update last_hit when webhook is triggered (regardless of success or failure)
            $this->webhook->update(['last_hit' => now()]);

            // Store webhook log on success/failure response
            $this->webhook->logs()->create([
                'webhook_event_id' => $this->event?->id,
                'payload'          => $this->payload,
                'request_headers'  => $headers, 
                'response_code'    => $response->status(),
                'response_body'    => $response->body(),
                'status'           => $response->successful() ? 'success' : 'failed',
                'delivered_at'     => now(),
            ]);
        } catch (\Exception $e) {
            // still update last hit on failure
            $this->webhook->update(['last_hit' => now()]); 
            
            // Store webhook log if the request completely fails
            $this->webhook->logs()->create([
                'webhook_event_id' => $this->event?->id,
                'payload'          => $this->payload,
                'request_headers'  => $headers,
                'response_code'    => 500,
                'response_body'    => $e->getMessage(),
                'status'           => 'failed',
                'delivered_at'     => now(),
            ]);
        }
    }

    /**
     * Prepare webhook headers
     */
    protected function getHeaders(): array
    {
        $headers = [
            'Content-Type'        => 'application/json',
            'X-Webhook-Event'     => strtolower($this->payload['event']['type']) . '.' . strtolower($this->payload['event']['action']),
            'X-Webhook-Timestamp' => now()->toIso8601String(),
            'user-agent'          => request()->userAgent() ?? null,
        ];

        $headers['Host'] = parse_url($this->webhook->url, PHP_URL_HOST);

        if ($this->webhook->secret) {
            // Create HMAC signature using SHA256
            $signature = hash_hmac('sha256', json_encode($this->payload), $this->webhook->secret);
            $headers['X-Webhook-Signature'] = $signature;
        }

        return $headers;
    }
}
