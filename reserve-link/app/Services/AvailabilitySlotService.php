<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;

class AvailabilitySlotService 
{
    /**
     * Generate all dates to check in the 30-day period.
     * 
     * @param Carbon $startDate Today.
     * @param Carbon $endDate 30 days in the future.
     * @return Collection Collection of the 30 next days.
     */
    public function generateDatesToCheck(Carbon $startDate, Carbon $endDate): Collection
    {
        $days = new Collection();
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            $days->push([
                'date' => $currentDate->copy(),
                'day_of_week' => $currentDate->dayOfWeek
            ]);
            $currentDate->addDay();
        }

        return $days;
    }

        /**
     * Generate potential slots based on user availabilities.
     * 
     * @param User $user
     * @param Collection $datesToCheck Collection of the 30 next days to check.
     * @param Carbon $now Now in the user timezone.
     * @return Collection List of potential slots based on user availabilities and 30 next days.
     */
    public function generatePotentialSlots(User $user, Collection $datesToCheck, Carbon $now): Collection
    {
        $datesByDayOfWeek = $datesToCheck->groupBy('day_of_week');

        return $user->availabilities->flatMap(function ($availability) use ($datesByDayOfWeek, $user, $now) {
            // Get all dates corresponding to this week day.
            $matchingDates = $datesByDayOfWeek->get($availability->day_of_week, collect([]));

            // Transform each date in a slot collection.
            return $matchingDates->flatMap(function ($dateInfo) use ($availability, $user, $now) {
                $date = $dateInfo['date'];
                $start = Carbon::parse($date->format('Y-m-d') . ' ' . $availability->start_time, $user->timezone);
                $end = Carbon::parse($date->format('Y-m-d') . ' ' . $availability->end_time, $user->timezone);

                // Ignore date if in the past.
                if ($end->lt($now)) {
                    return [];
                }

                // Calc all slots for this day.
                $period = CarbonPeriod::create(
                    $start,
                    $availability->slot_duration . ' minutes',
                    $end->subMinutes($availability->slot_duration)
                );

                // Transform the period to a slots collection.
                return collect($period)->map(function ($slotStart) use ($availability, $user, $now) {
                    $slotEnd = $slotStart->copy()->addMinutes($availability->slot_duration);

                    // Only keep future slots.
                    if ($slotStart->lte($now)) {
                        return null;
                    }

                    return [
                        'owner_id' => $user->id,
                        'start_time' => $slotStart->format('Y-m-d H:i:s'),
                        'start_time_utc' => $slotStart->copy()->setTimezone('UTC')->format('Y-m-d H:i:s'),
                        'end_time' => $slotEnd->format('Y-m-d H:i:s'),
                        'duration' => $availability->slot_duration,
                        'day_name' => $slotStart->format('l'),
                        'date' => $slotStart->format('Y-m-d'),
                    ];
                })->filter(); // Remove null values (past slots).
            });
        });
    }

    /**
     * Filter out booked slots.
     * 
     * @param Collection $potentialSlots List of potential slots based on user availabilities and 30 next days.
     * @param User $user
     * @return Collection List of all slots not booked and based on the user availabilities.
     */
    public function filterBookedSlots(Collection $potentialSlots, User $user): Collection
    {
        if ($potentialSlots->isEmpty()) {
            return $potentialSlots;
        }
        
        // Extract all start times to check in a single query
        $startTimesUtc = $potentialSlots->pluck('start_time_utc')->toArray();
        
        // Get all booked slots in a single query
        $bookedSlots = Booking::where('owner_id', $user->id)
            ->whereIn('start_time', $startTimesUtc)
            ->whereIn('status', ['confirmed', 'past'])
            ->pluck('start_time')
            ->toArray();
        
        // Filter out booked slots
        return $potentialSlots->filter(function ($slot) use ($bookedSlots) {
            return !in_array($slot['start_time_utc'], $bookedSlots);
        })->map(function ($slot) {
            // Remove temporary field
            unset($slot['start_time_utc']);
            return $slot;
        });
    }
}