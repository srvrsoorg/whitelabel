<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Installation\SiteSettingController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\User\AuthController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Public routes
Route::controller(PublicController::class)->group(function () {
    Route::post('/register', 'register'); // User register
    Route::post('/login', 'login'); // User login
    Route::get('/timezone', 'timezone'); // Get timezone
    Route::post('/forgot-password', 'forgotPassword'); // Forgot password
    Route::post('/reset-password', 'resetPassword'); // Reset password
    Route::get('/installation-steps', 'installationSteps'); // Provides installation steps
    Route::get('/enable-providers', 'enableProviders'); // Enable providers
    Route::get('/verify/{token}', 'verify'); // Verify Email
    Route::get("/countries", "countries");
    Route::get("/currencies","currencies");
});

Route::controller(SiteSettingController::class)->group(function () {
    Route::get('/site-setting', 'index'); // Retrieve site settings
});

// User routes (requires authentication)
Route::middleware(['auth:sanctum'])->group(function () {
    // User-related routes
    Route::controller(AuthController::class)->group(function () {
        Route::get('/me', 'show');
        Route::patch('/user/change-password', 'changePassword');
        Route::patch('/user/update', 'update');
        Route::get('/user/logout', 'logout');
        Route::post('/user/delete', 'delete');
    });
    Route::get('user/resend-verification-link', [PublicController::class, 'resendVerificationLink']);
});