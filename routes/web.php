<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Home Redirect
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Calendar PDF Export (Make Public)
Route::get('/admin/calendar/pdf', [App\Http\Controllers\CalendarController::class, 'exportPdf'])
    ->name('calendar.pdf');

Route::middleware('auth')->group(function () {
    // Admin & Management Routes
    Route::prefix('admin')->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('admin.dashboard'); // Primary name for Sidebar
        
        // Alias for compatibility
        Route::get('/home', function () {
            return redirect()->route('admin.dashboard');
        })->name('dashboard');

        Route::get('/calendar', \App\Livewire\CalendarView::class)->name('calendar.index');
        Route::get('/staff', \App\Livewire\Admin\Staff\StaffIndex::class)->name('staff.index');
        Route::get('/events', \App\Livewire\Admin\Events\EventIndex::class)->name('calendar.manage');
        Route::get('/line-notify', \App\Livewire\Admin\LineNotify\LineNotifyIndex::class)->name('line-notify.index');
    });

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});



// Redirect /admin to /admin/dashboard


require __DIR__.'/auth.php';
