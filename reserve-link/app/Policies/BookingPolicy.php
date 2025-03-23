<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    /**
     * Determine who can cancel a booking.
     */
    public function cancel(User $user, Booking $booking): bool
    {
        return $user->id === $booking->owner_id;
    }
}
