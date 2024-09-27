<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\User\{UserController, ActivityController, LoginHistoryController, TwoFaController, TransactionController, UsageSummaryController};

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

// Admin routes (requires authentication and adminOnly middleware)
Route::middleware(['auth:sanctum', 'adminOnly'])->prefix('/admin')->group(function () {

    // User routes (admin-only)
    Route::controller(UserController::class)->prefix('/users')->group(function () {
        Route::get('/', 'index');  // Get list of users
        Route::post('/', 'store'); // Create a user
        Route::get('/{user}', 'show'); // Show a user
        Route::patch('/{user}','update'); // Update a user
        Route::delete('/{user}','destory'); // Delete a user
        Route::post('/{user}/status-update/{action}', 'updateUserStatus'); // Update a user status 
    }); 

    Route::controller(UsageSummaryController::class)->prefix('users/{user}')->group(function () {
        Route::get('/usage-summaries', 'index');
    });

    Route::get('activities', [ActivityController::class,'index']);
    Route::get('users/{user}/activities', [ActivityController::class,'userIndex']);

    Route::get('users/{user}/login-history', [LoginHistoryController::class,'index']);

    Route::controller(TransactionController::class)->prefix('/users/{user}/transactions')->group( function () {
        Route::get('/', 'index'); // Get transactions of specific user
        Route::patch('/tax-details', 'calculateTaxAmount');
        Route::post('/', 'store');
        Route::get('/{transaction}', 'show');
        Route::patch('/{transaction}', 'update');
        Route::delete('/{transaction}', 'delete');
    });
    
    Route::get('/transactions', [TransactionController::class, 'allIndex']);        

    Route::controller(TwoFaController::class)->prefix('/users/{user}')->group(function() {
        Route::patch('/toggle-two-fa', 'toggle'); // Toggle two-factor authentication
        Route::get('/backup-codes', 'backupCodeShow');
        Route::get('/regenerate-backup-codes', 'regenerateBackupCodes');
        Route::get('/send-backup-codes', 'sendBackupCodeViaEmail');
    });

});