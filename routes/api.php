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

// Vue SPA API (protected by web session auth)
Route::middleware(['web', 'auth'])->prefix('vue')->group(function () {
    $vue = \App\Http\Controllers\Api\VueApiController::class;

    // Calendar
    Route::get('/calendar',              [$vue, 'calendarData']);
    Route::get('/calendar/event/{id}',   [$vue, 'calendarEventDetail']);

    // Staff
    Route::get('/staff/template',       [$vue, 'staffTemplate']);
    Route::post('/staff/import',        [$vue, 'staffImport']);
    Route::get('/staff',                [$vue, 'staffList']);
    Route::post('/staff',               [$vue, 'staffStore']);
    Route::post('/staff/reorder',       [$vue, 'staffReorder']);
    Route::post('/staff/{id}',          [$vue, 'staffUpdate']);
    Route::delete('/staff/{id}',        [$vue, 'staffDestroy']);

    // Events
    Route::get('/events',        [$vue, 'eventList']);
    Route::post('/events',       [$vue, 'eventStore']);
    Route::put('/events/{id}',   [$vue, 'eventUpdate']);
    Route::delete('/events/{id}',[$vue, 'eventDestroy']);

    // Line Notify
    Route::get('/line-notify',                    [$vue, 'lineNotifyData']);
    Route::get('/line-notify/events',             [$vue, 'lineNotifyEventsForDate']);
    Route::post('/line-notify/toggle-enabled',    [$vue, 'lineNotifyToggleEnabled']);
    Route::post('/line-notify/toggle-schedule',   [$vue, 'lineNotifyToggleSchedule']);
    Route::post('/line-notify/save-settings',     [$vue, 'lineNotifySaveSettings']);
    Route::post('/line-notify/test-token',        [$vue, 'lineNotifyTestToken']);
    Route::post('/line-notify/send-test',         [$vue, 'lineNotifySendTest']);
    Route::post('/line-notify/send-now',          [$vue, 'lineNotifySendNow']);
    Route::delete('/line-notify/log/{id}',        [$vue, 'lineNotifyDeleteLog']);
    Route::get('/line-notify/logs',               [$vue, 'lineNotifyLogs']);
});
