<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Admin\Staff\StaffIndex;
use App\Livewire\Admin\Events\EventIndex;
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

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

// Calendar PDF Export (Make Public)
Route::get('/calendar/pdf', [App\Http\Controllers\CalendarController::class, 'exportPdf'])
    ->name('calendar.pdf');

Route::middleware('auth')->group(function () {
    Route::get('/calendar', function () {
        return view('calendar.index');
    })->name('calendar.index');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin Only Routes
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Staff Management (Livewire)
    Route::get('/', StaffIndex::class)->name('dashboard');
    Route::get('/staff', StaffIndex::class)->name('staff.index');

    // Calendar Events Management (Livewire)
    Route::get('/events', EventIndex::class)->name('events.index');
});

require __DIR__.'/auth.php';
