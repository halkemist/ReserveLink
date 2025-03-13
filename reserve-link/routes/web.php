<?php

use App\Http\Controllers\AvailabilityController;
use App\Http\Controllers\AvailabilitySlotController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Availabilities
Route::get('/availabilities/{email}', [AvailabilitySlotController::class, 'showUserAvailabilities'])->name('calendar');

// Booking
Route::get('/booking/create', function () {
    return 'reservation done';
})->name('booking.create');

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/add-availability', function() {
        return view('calendar.add-availability');
    })->name('availability.add');
    Route::post('/availabilities/store', [AvailabilityController::class, 'store'])->name('availability.store');
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
