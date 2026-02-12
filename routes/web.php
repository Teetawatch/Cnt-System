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
    Route::get('/calendar', \App\Livewire\CalendarView::class)->name('calendar.index');
    
    // Admin Routes
    Route::get('/admin', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    Route::get('/admin/staff', \App\Livewire\Admin\Staff\StaffIndex::class)->name('staff.index');
    Route::get('/admin/events', \App\Livewire\Admin\Events\EventIndex::class)->name('calendar.manage');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});



// Redirect /admin to /admin/dashboard


require __DIR__.'/auth.php';
