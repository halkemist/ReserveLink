<?php

use App\Http\Controllers\AvailabilityController;
use App\Http\Controllers\AvailabilitySlotController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Models\Availability;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/**
 * Guest Routes
 */
Route::get('/', function () {
    return view('welcome');
});

// Availabilities
Route::get('/calendar/{email}', [AvailabilitySlotController::class, 'showUserAvailabilities'])->name('calendar');

// Booking
Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
Route::get('/booking/confirmation/{booking}', [BookingController::class, 'confirmation'])->name('booking.confirmation');
Route::delete('/booking/{booking}/cancel', [BookingController::class, 'cancel'])->name('booking.cancel');

/**
 * Authentified Routes
 */
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Dashboard - Add Availability
    Route::get('/dashboard/add-availability', function () {
        return view('calendar.add-availability');
    })->name('availability.add');
    Route::post('/availabilities/store', [AvailabilityController::class, 'store'])->name('availability.store');

    // Dashboard - Edit Availability
    Route::get('/availabilities/{availabilityId}', function ($availabilityId) { // TODO -> refacto function into a controller
        $availability = Availability::findOrFail($availabilityId);

        if ($availability->user_id !== Auth::id()) { // TODO -> policy
            abort(403, 'Unauthorized.');
        }

        return view('calendar.edit-availability', compact('availability'));
    })->name('availability.edit');
    Route::put('/availabilities/{availabilityId}', [AvailabilityController::class, 'update'])->name('availability.update');

    // Dashboard - Delete Availability
    Route::delete('/availabilities/{availabilityId}', [AvailabilityController::class, 'destroy'])->name('availability.destroy');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
