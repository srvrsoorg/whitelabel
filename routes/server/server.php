<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Server\{ServerController, PanelController};

// Define routes within a middleware group that requires authentication via Sanctum
Route::middleware(['auth:sanctum'])->group(function () {

    Route::get('/servers/{server}/reconnect', [ServerController::class, 'serverReconnect']);
    Route::patch('servers/{server}/update', [ServerController::class, 'serverUpdate']);
    
    // Map the 'servers' resource to the 'ServerController' class
    Route::resource('servers', ServerController::class);
    
    // Define a route for the index function of PanelController
    Route::get('servers/{server}/panel', [PanelController::class, 'index']);
});
