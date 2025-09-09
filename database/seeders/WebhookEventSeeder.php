<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Admin\Webhook\WebhookEvent;

class WebhookEventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $events = [
            // User events
            ['name' => 'user.created', 'type' => 'User', 'action' => 'Created'],
            ['name' => 'user.udpated', 'type' => 'User', 'action' => 'Updated'],
            ['name' => 'user.deleted', 'type' => 'User', 'action' => 'Deleted'],

            // Server events
            ['name' => 'server.created', 'type' => 'Server', 'action' => 'Created'],
            ['name' => 'server.deleted', 'type' => 'Server', 'action' => 'Deleted'],

            // Transaction events
            ['name' => 'transaction.created', 'type' => 'Transaction', 'action' => 'Created'],
            ['name' => 'transaction.success', 'type' => 'Transaction', 'action' => 'Success'],
            ['name' => 'transaction.failed', 'type' => 'Transaction', 'action' => 'Failed'],
            ['name' => 'transaction.refunded', 'type' => 'Transaction', 'action' => 'Refunded'],

            // Ticket events
            ['name' => 'ticket.created', 'type' => 'Ticket', 'action' => 'Created'],
            ['name' => 'ticket.closed', 'type' => 'Ticket', 'action' => 'Closed'],
            ['name' => 'ticket.reopened', 'type' => 'Ticket', 'action' => 'Reopened'],
        ];

        foreach ($events as $event) {
            WebhookEvent::updateOrCreate(
                ['type' => $event['type'], 'action' => $event['action']],
                ['name' => $event['name']]
            );
        }
    }
}
