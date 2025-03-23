<?php

namespace App\Policies;

use App\Models\Availability;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AvailabilityPolicy
{
    /**
     * Determine whether the user can update the availability.
     * 
     * @param User $user
     * @param Availability $availability
     * @return bool
     */
    public function update(User $user, Availability $availability): bool
    {
        return $user->id === $availability->user_id;
    }

    /**
     * Determine whether the user can delete the availability.
     * 
     * @param User $user
     * @param Availability $availability
     * @return bool
     */
    public function delete(User $user, Availability $availability): bool
    {
        return $user->id === $availability->user_id;
    }
}
