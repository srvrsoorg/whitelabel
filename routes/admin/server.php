<?php 

use App\Http\Controllers\Admin\Server\{ServerController, SubscriptionController};

Route::middleware(['auth:sanctum', 'adminOnly'])->prefix('admin')->group( function () {
	Route::controller(ServerController::class)->prefix('servers')->group( function () {
		Route::get('/', 'index');
		Route::get('/{server}', 'show');
		Route::delete('/{server}', 'destroy');
	});

	Route::controller(SubscriptionController::class)->prefix('users/{user}/servers/{server}')->group( function () {
		Route::patch('/subscriptions/{subscription}', 'update');
	});
});