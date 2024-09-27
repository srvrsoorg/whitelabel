<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;

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

Route::middleware(['auth:sanctum', 'adminOnly'])->controller(DashboardController::class)->prefix('admin/dashboard')->group(function () {
    Route::get('/summary/{model}', 'getSumary');
    Route::get('/chart/{model}/{filter}', 'getChartData');
    Route::get('/users-by-country', 'usersByCountry');
    Route::get('/server-connection-counts', 'serverConnectionCounts');
});