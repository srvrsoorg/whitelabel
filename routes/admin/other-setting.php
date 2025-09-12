<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Configuration\OtherSettingController;

/*
|--------------------------------------------------------------------------
| Admin API Routes
|--------------------------------------------------------------------------
| Protected routes for admin policy settings (Terms, Privacy, Refund).
*/

Route::middleware(['auth:sanctum', 'adminOnly'])->prefix('admin')->controller(OtherSettingController::class)->group(function () {
    Route::get('/links', 'index');
    Route::post('/links', 'store');
});