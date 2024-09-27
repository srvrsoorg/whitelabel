<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\TransactionController;

Route::middleware(['auth:sanctum'])->controller(TransactionController::class)->prefix('user/transactions')->group( function () {
    Route::get('/', 'index');
    Route::get('/{key}', 'show');
    Route::post('/', 'store');
    Route::post("/checkout", "checkout");
    Route::post('/execute/{key}', 'execute');
    Route::post('/verify/{key}', 'verify');
});

Route::get('promo-codes/{promo_code}', [TransactionController::class, 'promocodeShow'])->middleware(['auth:sanctum']);