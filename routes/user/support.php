<?php

use App\Http\Controllers\User\{TicketController, ReplyController};

Route::middleware(['auth:sanctum'])->prefix('/user')->group( function () {
	Route::controller(TicketController::class)->prefix('/tickets')->group( function () {
	    Route::get('/', 'index');
	    Route::post('/', 'store');
	    Route::get('/{ticket}', 'show');
	    Route::patch('/{ticket}/{action}', 'statusUpdate');
	});
	Route::get('/server-ids', [TicketController::class, 'serverIds']);

	Route::controller(ReplyController::class)->prefix('/tickets/{ticket}')->group( function () {
		Route::get('/replies', 'index');
		Route::post('/reply', 'store');
	});
});