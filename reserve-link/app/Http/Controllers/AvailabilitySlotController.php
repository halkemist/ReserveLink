<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

/**
 * Manage slot booking (guest and auth users).
 */
class AvailabilitySlotController extends Controller
{
    public function showUserAvailabilities($email)
    {
        // Find the user with email and load availabilities
        $user = User::where('email', $email)->with('availabilities')->first();

        if (!$user) {
            abort(404, "User not found");
        }

        // Look at next 30 days only
        $startDate = Carbon::today();
        $endDate = $startDate->copy()->addDays(30);
        $slots = [];
        $now = Carbon::now($user->timezone);

        foreach ($user->availabilities as $availability) {
            // Find first day that match user day setting
            $currentDate = $startDate->copy();
            while ($currentDate->dayOfWeek != $availability->day_of_week) {
                $currentDate->addDay();
            }

            // Look at all days in 30 days period
            while ($currentDate <= $endDate) {
                $start = Carbon::parse($currentDate->format('Y-m-d') . ' ' . $availability->start_time, $user->timezone);
                $end = Carbon::parse($currentDate->format('Y-m-d') . ' ' . $availability->end_time, $user->timezone);
                
                // Make small time slots
                $period = CarbonPeriod::create($start, $availability->slot_duration . ' minutes', $end->subMinutes($availability->slot_duration));

                foreach ($period as $slotStart) {
                    $slotEnd = $slotStart->copy()->addMinutes($availability->slot_duration);
                    
                    // Change to UTC time for db check
                    $slotStartUtc = $slotStart->copy()->setTimezone('UTC');
                    
                    // Only show slots in future and not booked
                    if ($slotStart->gt($now) && 
                        !Booking::where('owner_id', $user->id)
                            ->where('start_time', $slotStartUtc)
                            ->whereIn('status', ['confirmed', 'past'])
                            ->exists()) {
                            
                        $slots[] = [
                            'owner_id' => $user->id,
                            'start_time' => $slotStart->format('Y-m-d H:i:s'),
                            'end_time' => $slotEnd->format('Y-m-d H:i:s'),
                            'duration' => $availability->slot_duration,
                            'day_name' => $slotStart->format('l'),
                            'date' => $slotStart->format('Y-m-d'),
                        ];
                    }
                }
                
                // Go to the next week same day
                $currentDate->addDays(7);
            }
        }

        // Sort slots by time
        usort($slots, function ($a, $b) {
            return strcmp($a['start_time'], $b['start_time']);
        });

        return view('calendar.calendar', compact('slots', 'user'));
    }
}
