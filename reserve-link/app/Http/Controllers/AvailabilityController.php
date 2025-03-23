<?php

namespace App\Http\Controllers;

use App\Http\Requests\AvailabilityRequest;
use App\Models\Availability;
use Illuminate\Support\Facades\Auth;

/**
 * Controller for managing user availability slots.
 *
 * Handles the create, update and delete of time slots of users availabilities for bookings.
 */
class AvailabilityController extends Controller
{
    /**
     * Store a new availability slot.
     *
     * Validate request, check timeslot overlap and create the new availability record.
     *
     * @param  AvailabilityRequest  $request  Validated form request.
     * @return RedirectResponse Redirects back with errors or to dashboard with success message.
     */
    public function store(AvailabilityRequest $request)
    {
        $authUserId = Auth::id();
        $validated = $request->validated();

        // Check if a slot exist at the same time for the same day
        $existingOverlap = Availability::where('user_id', $authUserId)
            ->where('day_of_week', $validated['day_of_week'])
            ->where(function ($q) use ($validated) {
                $q->where('start_time', '<', $validated['end_time'])
                    ->where('end_time', '>', $validated['start_time']);
            })->first();

        if ($existingOverlap) {
            return back()
                ->withInput()
                ->withErrors(['overlap' => 'This new time slot overlaps an existing one.']);
        }

        Availability::create([
            'user_id' => $authUserId,
            'day_of_week' => $validated['day_of_week'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'slot_duration' => $validated['slot_duration'],
        ]);

        return redirect()
            ->route('dashboard')
            ->with('success', 'Your availability has been successfully added.');
    }

    /**
     * Update an existing availability slot.
     *
     * Get the requested availability, authorize the user and update the record with validated data.
     *
     * @param  int  $availabilityId  The ID of the availability slot to update.
     * @param  AvailabilityRequest  $request  Validated form request.
     * @return RedirectResponse Redirects to dashboard with success message.
     *
     * @throws AuthorizationException If user not authotized.
     * @throws ModelNotFoundException If availability not found.
     */
    public function update($availabilityId, AvailabilityRequest $request)
    {
        $availability = Availability::findOrFail($availabilityId);

        $this->authorize('update', $availability);

        $availability->update($request->validated());

        return redirect()
            ->route('dashboard')
            ->with('success', 'Availability updated successfully');
    }

    /**
     * Delete an existing availability slot.
     *
     * Get the requested availability, authorize the user and delete the record from the DB.
     *
     * @param  int  $availabilityId  The ID of the availability slot to delete.
     * @return RedirectResponse Redirects to dashboard with success message.
     *
     * @throws AuthorizationException If user not authorized.
     * @throws ModelNotFoundException If availability not found.
     */
    public function destroy($availabilityId)
    {
        $availability = Availability::findOrFail($availabilityId);

        $this->authorize('delete', $availability);

        $availability->delete();

        return redirect()
            ->route('dashboard')
            ->with('success', 'Your availability has been successfully removed.');
    }
}
