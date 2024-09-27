<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Server\CloudProviderController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group.
|
*/

Route::middleware(['auth:sanctum'])->controller(CloudProviderController::class)->prefix('cloud-providers')->group(function () {
    Route::get('/', 'index');
    Route::get('/{cloudProvider}/regions', 'regions');
    Route::get('/{cloudProvider}/sizes', 'sizes');
});