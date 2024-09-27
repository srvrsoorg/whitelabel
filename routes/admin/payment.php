<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Configuration\PaymentController;
use App\Http\Controllers\Admin\PromoCodeController;

Route::middleware(['auth:sanctum', 'adminOnly'])->prefix('admin')->group(function () {
    Route::controller(PaymentController::class)->prefix('payments')->group( function () {
        Route::post('/', 'store');
		Route::get('/', 'index');
		Route::patch('/{payment}', 'update');
    });

    Route::controller(PromoCodeController::class)->prefix('/promo-codes')->group( function () {
        Route::post('/', 'store');
        Route::get('/', 'index');
        Route::patch('/{promoCode}', 'update');
        Route::delete('/{promoCode}', 'delete');
        Route::get('/available', 'availablePromoCode');
        Route::get('/{promoCode}/users/{user}', 'show');
    });
});