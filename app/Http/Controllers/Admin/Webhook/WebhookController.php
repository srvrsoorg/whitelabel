<?php

namespace App\Http\Controllers\Admin\Webhook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Webhook\Webhook;
use App\Models\Admin\Webhook\WebhookEvent;
use Database\Seeders\WebhookEventSeeder;
use Illuminate\Support\Facades\Artisan;
use App\Services\WebhookService;
use App\Http\Helper;

class WebhookController extends Controller
{
     /**
     * Display a list of all webhooks with their events.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request) {
        try {
            $perPage = $request->input('per_page', 10);
            $webhooks = Webhook::with('events:id,name')->paginate($perPage);

            return response()->json([
                "webhooks" => $webhooks
            ], 200);
        } catch(\Exception $e) {
            report($e);
            return response()->json([
                "message" => "Something went wrong!"
            ], 500);
        }
    }

    /**
     * Store a newly created webhook with its associated events.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request) {
        $request->validate([
            'name' => 'required|string',
            'url' => 'required|url',
            'secret' => 'nullable|string',
            'status' => 'required|boolean',
            'event_ids' => 'required|array|min:1',
            'event_ids.*' => 'exists:webhook_events,id',

        ]);
        
        try {
            $webhook = Webhook::create($request->only(['name','url', 'secret', 'status']));

            $webhook->events()->sync($request->event_ids);

            Helper::adminActivity(auth()->user(), 'Webhook', 'Create', 'Webhook ' . ($webhook->name) . ' has been created successfully.');

            return response()->json([
                "message" => "Webhook ({$webhook->name}) has been created successfully."
            ], 200);
        } catch(\Exception $e) {
            report($e);
            return response()->json([
                "message" => "Something went wrong!"
            ], 500);
        }
    }

    /**
     * Display a specific webhook with limited details (id, name, url) 
     * along with its associated events (id, name only).
     *
     * @param  Webhook  $webhook
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Webhook $webhook) {
        try {
            // Load only the events with id and name
            $webhook->load('events:id,name');

            // Select only the fields you want from webhook
            $webhookData = $webhook->only(['id', 'name', 'url']);

            // Attach the events
            $webhookData['events'] = $webhook->events->map(function($event) {
                return [
                    'id' => $event->id,
                    'name' => $event->name,
                ];
            });

            return response()->json([
                "webhook" => $webhookData
            ], 200);
        } catch(\Exception $e) {
            report($e);
            return response()->json([
                "message" => "Something went wrong!"
            ], 500);
        }
    }

    /**
     * Update an existing webhook and its associated events.
     *
     * @param Request $request
     * @param Webhook $webhook
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Webhook $webhook) {
        $request->validate([
            'name' => 'required|string',
            'url' => 'required|url',
            'secret' => 'nullable|string',
            'status' => 'required|boolean',
            'event_ids' => 'required|array|min:1',
            'event_ids.*' => 'exists:webhook_events,id',
        ]);

        try {
            $webhook->update($request->only(['name','url','secret', 'status']));

            $webhook->events()->sync($request->event_ids);

            Helper::adminActivity(auth()->user(), 'Webhook', 'Update', 'Webhook ' . ($webhook->name) . ' has been updated successfully.');

            return response()->json([
                "message" => "Webhook ({$webhook->name}) has been updated successfully."
            ], 200);
        } catch(\Exception $e) {
            report($e);
            return response()->json([
                "message" => "Something went wrong!"
            ], 500);
        }
    }

    /**
     * Delete a webhook by its ID.
     *
     * @param Request $request
     * @param Webhook $webhook
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Webhook $webhook) {
        try {
            $webhookName = $webhook->name;
            $webhook->delete();
            
            Helper::adminActivity(auth()->user(), 'Webhook', 'Delete', 'Webhook ' . ($webhookName) . ' has been deleted successfully.');

            return response()->json([
                "message" => "Webhook ({$webhookName}) has been deleted successfully."
            ], 200);
        } catch(\Exception $e) {
            report($e);
            return response()->json([
                "message" => "Something went wrong!"
            ], 500);
        }
    }

    /**
     * Toggle the status of a webhook (enable/disable).
     *
     * @param  \App\Models\Admin\Webhook\Webhook  $webhook
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleWebhook(Webhook $webhook) {
        try {
            // Toggle the status
            $webhook->status = !$webhook->status;
            $webhook->save();

            // Determine status text for response
            $status = $webhook->status ? 'enabled' : 'disabled';

            Helper::adminActivity(auth()->user(), 'Webhook', 'Update', 'Webhook ' . ($webhook->name) . ' has been ' . $status . '.');

            return response()->json([
                'message' => "Webhook ({$webhook->name}) has been {$status} successfully."
            ], 200);
        } catch(\Exception $e) {
            report($e);
            return response()->json([
                "message" => "Something went wrong!"
            ], 500);
        }
    }

     /**
     * Get all available webhook events.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEvents() {
        try{
            // Seed only if no events exist
            if (!WebhookEvent::exists()) {
                Artisan::call('db:seed', [
                    '--class' => WebhookEventSeeder::class,
                ]);
            }

            $events = WebhookEvent::all();

            return response()->json([
                "events" => $events
            ], 200);
        } catch(\Exception $e) {
            report($e);
            return response()->json([
                "message" => "Something went wrong!"
            ], 500);
        }
    }

    /**
     * Retrieve the logs/history for a specific webhook.
     *
     * @param Request $request
     * @param Webhook $webhook
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLogs(Request $request, Webhook $webhook) {
        try {
            $perPage = $request->input('per_page', 10);

            $logs = $webhook->logs()
                ->orderByDesc('created_at')
                ->paginate($perPage);

            return response()->json([
                'logs' => $logs
            ], 200);
        } catch(\Exception $e) {
            report($e);
            return response()->json([
                "message" => "Something went wrong!"
            ], 500);
        }
    }

    /**
     * Trigger a test webhook for the given webhook.
     * @param  \App\Models\Admin\Webhook\Webhook  $webhook
     * @param  \App\Services\WebhookService       $webhookService
     * @return \Illuminate\Http\JsonResponse
     */
    public function testWebhook(Webhook $webhook, WebhookService $webhookService) {
        try {
            $webhookService->test($webhook);

            return response()->json([
                "message" => "Test webhook for ({$webhook->name}) triggered successfully."
            ]);
        } catch(\Exception $e) {
            report($e);
            return response()->json([
                "message" => "Something went wrong!"
            ], 500);
        }
    }
}
