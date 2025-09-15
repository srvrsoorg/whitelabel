<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Admin\Webhook\Webhook;
use App\Models\Admin\Webhook\WebhookEvent;
use App\Jobs\Admin\Webhook\SendWebhook;

class WebhookService
{
    /**
     * Send a webhook event to all subscribed and active webhooks.
     *
     * @param  string  $type     The type of the event (e.g., user, server, payment).
     * @param  string  $action   The action performed (e.g., created, updated, deleted).
     * @param  array   $payload  Additional data to send with the webhook.
     * @return void
     */
    public function send(string $type, string $action, array $payload = []): void
    {
        // Check if event exists
        $event = WebhookEvent::where('type', $type)
            ->where('action', $action)
            ->first();

        if (!$event) {
            \Log::warning("No webhook event found for {$type}.{$action}");
            return;
        }

        // Get all webhooks subscribed to this event
        $webhooks = $event->webhooks()->where('status', true)->get();

        if ($webhooks->isEmpty()) {
            return; // No active webhooks found
        }

        // Prepare standardized payload
        $finalPayload = [
            'event' => [
                'name'   => $event->name,
                'type'   => $type,
                'action' => $action,
                'timestamp' => now(),
            ],
            'payload' => $payload,
            'message' => $this->formatMessage($type, $action, $payload),
        ];

        // Dispatch job for each webhook subscribed to this event
        foreach ($webhooks as $webhook) {
            SendWebhook::dispatch($webhook, $event, $finalPayload)->delay(5);
        }
    }

    /**
     * Format a professional webhook message
     */
    protected function formatMessage(string $type, string $action, array $payload): string
    {
        return match (strtolower($type)) {
            'transaction' => match (strtolower($action)) {
                'created'  => "A new transaction has been created and is pending.",
                'success'  => "Transaction completed successfully via {$payload['transaction']['payment_gateway']}.",
                'failed'   => "Transaction failed using {$payload['transaction']['payment_gateway']}.",
                'refunded' => "Transaction was refunded. Reason: " . ($payload['transaction']['refund_reason'] ?? 'N/A') . ".",
                default    => "Transaction event: {$action}.",
            },
            'user' => match (strtolower($action)) {
                'created' => "New user registered with email {$payload['user']['email']}.",
                'updated' => "User profile ({$payload['user']['email']}) has been updated.",
                'deleted' => "User account ({$payload['user']['email']}) has been deleted.",
                default   => "User event: {$action}.",
            },
            'server' => match (strtolower($action)) {
                'created' => "Server({$payload['server']['name']}) has been created successfully.",
                'deleted' => "Server({$payload['server']['name']}) has been deleted.",
                default   => "Server event: {$action}.",
            },
            'ticket' => match (strtolower($action)) {
                'created' => "A new ticket has been created: {$payload['ticket']['title']}.",
                'closed' => "Ticket({$payload['ticket']['title']}) has been closed.",
                'reopened' => "Ticket({$payload['ticket']['title']}) has been re-opened.",
                default   => "Ticket event: {$action}.",
            },
            default => ucfirst($type) . " {$action} event triggered.",
        };
    }

    /**
     * Trigger a test webhook for a given webhook.
     */
    public function test(Webhook $webhook): ?array
    {
        if (!$webhook->status) {
            \Log::info("Skipping test webhook for ({$webhook->name}) because it is disabled.");
            return null; // don’t dispatch job
        }

        $payload = [
            'event' => [
                'name'      => 'test.webhook',
                'type'      => 'Webhook',
                'action'    => 'Test',
                'timestamp' => now(),
            ],
            'payload' => [
                'id'   => $webhook->id,
                'name' => $webhook->name,
            ],
            'message' => 'This is a test webhook payload.',
        ];

        // Reuse same job, just pass type/action like real events
        SendWebhook::dispatch($webhook, null, $payload)->delay(1);

        return $payload;
    }
}
