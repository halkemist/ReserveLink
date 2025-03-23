<?php

namespace App\Policies;

use App\Models\Availability;
use App\Models\User;

class AvailabilityPolicy
{
    /**
     * Determine whether the user can update the availability.
     */
    public function update(User $user, Availability $availability): bool
    {
        return $user->id === $availability->user_id;
    }

    /**
     * Determine whether the user can delete the availability.
     */
    public function delete(User $user, Availability $availability): bool
    {
        return $user->id === $availability->user_id;
    }
}
