<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BookingPolicy
{
    /**
     * Determine who can cancel a booking.
     * 
     * @param User $user
     * @param Booking $booking
     * @return bool
     */
    public function cancel(User $user, Booking $booking): bool
    {
        return $user->id === $booking->owner_id;
    }
}
