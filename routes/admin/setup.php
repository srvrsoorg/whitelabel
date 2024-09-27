<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Installation\{InstallationController, SmtpController, DatabaseController, SiteSettingController};
use App\Http\Controllers\Admin\Configuration\TaxController;

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

// Installation routes
Route::prefix('setup')->group(function () {
    Route::controller(InstallationController::class)->group(function() {
        // Registration route
        Route::post('/register','register');

        // Permission Route
        Route::get('/permission', 'checkPermission');
        
        // Basic Details routes
        Route::post('/verify', 'verify'); // Verify basic details
    });

    // SMTP routes
    Route::controller(SmtpController::class)->group(function () {
        Route::get('/smtp', 'index');
        Route::post('/smtp', 'store'); // Store SMTP settings
    });

    // Database routes
    Route::controller(DatabaseController::class)->group(function () {
        Route::post('/database', 'store');   // Store database
        Route::get('/database', 'index');    // Get database
    });
});

// Admin routes (requires authentication and adminOnly middleware)
Route::middleware(['auth:sanctum', 'adminOnly'])->prefix('/admin')->group(function () {

    Route::controller(SiteSettingController::class)->group(function () {
        Route::get('/billing-details', 'BillingIndex'); // Get Billing-details
        Route::get('/get-organizations', 'getOrganization'); // Get Organizations list
        Route::get('/setup', 'setup'); // Get Setup List
        Route::post('/site-setting', 'store'); // Store site-setting
        Route::patch('/site-setting', 'update'); // Update site-setting
        Route::post('/billing-details', 'billingDetailStore'); // Store Billing-details
    });

    Route::controller(TaxController::class)->prefix("tax")->group(function () {
        Route::post("/", "store");
        Route::get("/", "index");
    });

    // SMTP routes (admin-only)
    Route::controller(SmtpController::class)->prefix('smtp')->group(function () {
        Route::get('/', 'index'); // Get SMTP settings
        Route::post('/', 'store'); // Store SMTP settings
        Route::get('/testMail', 'testMail'); // Test SMTP mail sending
    });
});
