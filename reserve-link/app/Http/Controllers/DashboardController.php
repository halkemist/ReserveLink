<?php

namespace App\Http\Controllers;

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
            0 => 'Sunday',
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
        ];
        
        return view('dashboard', compact('upcomingBookings', 'availabilities', 'dayNames'));
    }
}
