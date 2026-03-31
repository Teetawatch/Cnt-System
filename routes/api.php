<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Mobile App API (public — read-only schedule data)
Route::prefix('mobile')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Api\MobileApiController::class, 'dashboard']);
    Route::get('/staff', [\App\Http\Controllers\Api\MobileApiController::class, 'staffList']);
    Route::get('/staff/{staffId}/events', [\App\Http\Controllers\Api\MobileApiController::class, 'staffEvents']);
    Route::get('/stats', [\App\Http\Controllers\Api\MobileApiController::class, 'stats']);
    Route::get('/search', [\App\Http\Controllers\Api\MobileApiController::class, 'searchEvents']);
});

// FCM Token registration (public — device push token)
Route::prefix('fcm')->group(function () {
    Route::post('/token', [\App\Http\Controllers\Api\FcmController::class, 'registerToken']);
    Route::delete('/token', [\App\Http\Controllers\Api\FcmController::class, 'removeToken']);
});
