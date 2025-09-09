<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Webhook\WebhookController;

/*
|--------------------------------------------------------------------------
| Admin Webhook Routes
|--------------------------------------------------------------------------
|
| These routes are used to manage webhooks from the admin panel.
| Only authenticated admin users can access them.
|
*/
Route::middleware(['auth:sanctum', 'adminOnly'])->controller(WebhookController::class)->prefix('admin/webhooks')->group( function () {
		Route::get('/', 'index');
		Route::post('/', 'store');
		Route::get('/events', 'getEvents');
		Route::get('/{webhook}', 'show');
		Route::patch('/{webhook}', 'update');
		Route::delete('/{webhook}', 'destroy');
		Route::patch('/{webhook}/toggle', 'toggleWebhook');
		Route::get('/{webhook}/logs', 'getLogs');
		Route::get('/{webhook}/test', 'testWebhook');
});