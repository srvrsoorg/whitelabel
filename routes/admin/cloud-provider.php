<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Configuration\{CloudProviderController, CloudProviderPlanController};

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

Route::middleware(['auth:sanctum', 'adminOnly'])->prefix('admin')->group(function () {
    Route::resource('cloud-providers',CloudProviderController::class);

    Route::controller(CloudProviderController::class)->prefix('cloud-providers')->group( function () {
        Route::get('/generate-key/{server-key-name}', 'generateKey');
        Route::get('/{cloud_provider}/regions', 'getRegions');
        Route::get('/{cloud_provider}/sizes', 'getSizes');
    });

    Route::controller(CloudProviderPlanController::class)->prefix('/cloud-providers/{cloudProvider}')->group( function () {
        Route::get('/plans', 'index');
        Route::post('/plans', 'store');
    });
});