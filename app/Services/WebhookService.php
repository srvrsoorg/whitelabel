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
            'message'   => $this->formatMessage($type, $action, $payload),
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
                'created'  => "Transaction(#{$payload['id']}) has been created and is pending.",
                'success'  => "Transaction(#{$payload['id']}) completed successfully via {$payload['payment_gateway']}.",
                'failed'   => "Transaction(#{$payload['id']}) failed using {$payload['payment_gateway']}.",
                'refunded' => "Transaction(#{$payload['id']}) was refunded. Reason: " . ($payload['refund_reason'] ?? 'N/A') . ".",
                default    => "Transaction(#{$payload['id']}) event: {$action}.",
            },
            'user' => match (strtolower($action)) {
                'created' => "New user registered: {$payload['email']} (ID: {$payload['id']}).",
                'deleted' => "User(#{$payload['id']}) account has been deleted.",
                default   => "User event: {$action}.",
            },
            'server' => match (strtolower($action)) {
                'created' => "Server(#{$payload['id']}) has been created successfully.",
                'deleted' => "Server(#{$payload['id']}) has been deleted.",
                default   => "Server event: {$action}.",
            },
            'ticket' => match (strtolower($action)) {
                'created' => "Ticket(#{$payload['id']}) has been created successfully.",
                'closed' => "Ticket(#{$payload['id']}) has been closed.",
                default   => "Ticket event: {$action}.",
            },
            default => ucfirst($type) . " {$action} event triggered.",
        };
    }

    /**
     * Trigger a test webhook for a given webhook.
     */
    public function test(Webhook $webhook): array
    {
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
