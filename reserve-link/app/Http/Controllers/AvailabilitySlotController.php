<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AvailabilitySlotService;
use Carbon\Carbon;
use Illuminate\View\View;

/**
 * Controller for displaying availability slots.
 *
 * Handles the display of available time slots for booking, taking into account user availabilities, timezone differences and existing bookings.
 * Accessible to guests and auth users.
 */
class AvailabilitySlotController extends Controller
{
    protected $availabilitySlotService;

    public function __construct(AvailabilitySlotService $availabilitySlotService)
    {
        $this->availabilitySlotService = $availabilitySlotService;
    }

    /**
     * Display available booking slots for a specific user.
     *
     * Find a user by email, calculate his availabilities for the next 30 days based on his configured weekly schedule, filter out past slots and already booked slots, present them into chronological order.
     *
     * @param  string  $email  Email address of the user whose availabilities are checked.
     * @return View Calendar view with available slots.
     *
     * @throws ModelNotFoundException If user not found.
     */
    public function showUserAvailabilities($email): View
    {
        // Find the user with email and load availabilities
        $user = User::where('email', $email)->with('availabilities')->firstOrFail();

        // Look at next 30 days only
        $startDate = Carbon::today();
        $endDate = $startDate->copy()->addDays(30);
        $now = Carbon::now($user->timezone);

        // Prepare dates to check, slots and already booked slots
        $datesToCheck = $this->availabilitySlotService->generateDatesToCheck($startDate, $endDate);
        $potentialSlots = $this->availabilitySlotService->generatePotentialSlots($user, $datesToCheck, $now);
        $slots = $this->availabilitySlotService->filterBookedSlots($potentialSlots, $user);

        // Sort by date
        $slots = $slots->sortBy('start_time')->values()->all();

        return view('calendar.calendar', compact('slots', 'user'));
    }
}
