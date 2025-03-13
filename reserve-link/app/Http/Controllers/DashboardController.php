<?php

namespace App\Http\Controllers;

use App\Http\Requests\AvailabilityRequest;
use App\Models\Availability;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

/**
 * Manage availability slots (authentified user).
 */
class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get upcoming appointments
        $upcomingBookings = Booking::where(function($query) use ($user) {
                $query->where('owner_id', $user->id)
                    ->orWhere('user_id', $user->id);
            })
            ->where('start_time', '>', now())
            ->orderBy('start_time', 'asc')
            ->get();
        
        // Get availabilities
        $availabilities = Availability::where('user_id', $user->id)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();
        
        // Week days
        $dayNames = [
            0 => 'Dimanche',
            1 => 'Lundi',
            2 => 'Mardi',
            3 => 'Mercredi',
            4 => 'Jeudi',
            5 => 'Vendredi',
            6 => 'Samedi',
        ];
        
        return view('dashboard', compact('upcomingBookings', 'availabilities', 'dayNames'));
    }

    public function store(AvailabilityRequest $request)
    {
        $authUser = Auth::user();
        $authUser->availabilities->create($request->validated());
    }
}
