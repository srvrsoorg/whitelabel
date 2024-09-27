<?php 

use App\Http\Controllers\Admin\{TicketController, ReplyController};

Route::middleware(['auth:sanctum', 'adminOnly'])->prefix('/admin')->group( function () {
	Route::controller(TicketController::class)->prefix('/tickets')->group( function () {
		Route::get('/', 'index');
		Route::get('/{ticket}', 'show');
		Route::patch('/{ticket}/{action}', 'statusUpdate');
	});

	Route::controller(ReplyController::class)->prefix('/tickets/{ticket}')->group( function () {
		Route::post('/reply', 'store');
		Route::get('/replies','index');
	});
});