<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\{TwoFaController, ActivityController, LoginHistoryController, BillingDetailController, UsageSummaryController, CreditReminderController};

// Routes with 'auth:sanctum' middleware
Route::middleware(['auth:sanctum'])->controller(TwoFaController::class)->group(function () {
    Route::patch('/toggle-two-fa', 'toggle'); // Toggle two-factor authentication
    Route::get('/backup-codes', 'backupCodeShow');
    Route::get('/regenerate-backup-codes', 'regenerateBackupCodes');
    Route::get('/send-backup-codes', 'sendBackupCodeViaEmail');
    Route::get('/qr-code', 'qrCode'); // Generate QR code for two-factor authentication
    Route::post('/toggle-google-twofa', 'googleTwofaToggle'); // Toggle Google two-factor authentication
});

Route::controller(TwoFaController::class)->group(function () {
    Route::post('/two-fa/resend', 'twoFaResend'); // Resend two-factor authentication code
    Route::post('/two-fa/verify', 'twoFaVerify'); // Verify two-factor authentication code
});

Route::middleware(['auth:sanctum'])->controller(ActivityController::class)->group(function () {
    Route::get('/activities', 'userIndex');
    Route::get('/servers/{server}/activities', 'serverIndex');
    Route::get('/servers/{server}/applications/{application}/activities', 'applicationIndex');
});

Route::middleware(['auth:sanctum'])->controller(LoginHistoryController::class)->group(function () {
    Route::get('/login-history', 'index');
});

// Routes for managing billing details
Route::middleware(['auth:sanctum'])->controller(BillingDetailController::class)->group(function () {
    Route::get('/billing-details', 'index');
    Route::patch('/billing-details', 'update');
});

// Define a route for the UsageSummaryController
Route::middleware(['auth:sanctum'])->controller(UsageSummaryController::class)->group(function () {
    Route::get('/usage-summaries', 'index');
});

Route::middleware(['auth:sanctum'])->controller(CreditReminderController::class)->group(function () {
    Route::patch('/reminder-minimum-credit', 'reminderCredit');
});