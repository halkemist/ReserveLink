<?php

namespace App\Http\Controllers;

use App\Http\Requests\AvailabilityRequest;
use App\Models\Availability;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AvailabilityController extends Controller
{
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
            return back()->withInput()->withErrors(['overlap' => 'This new time slot overlaps an existing one.']);
        }

        Availability::create([
            'user_id' => $authUserId,
            'day_of_week' => $validated['day_of_week'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'slot_duration' => $validated['slot_duration'],
        ]);

        return redirect()->route('dashboard')->with('success', 'Your availability has been successfully added.');
    }

    public function update($availabilityId, AvailabilityRequest $request)
    {
        $authUserId = Auth::id();

        $availability = Availability::findOrFail($availabilityId);
        if ($availability->user_id !== $authUserId) { // TODO -> policy
            return abort(403, 'Unauthorized');
        }
        $availability->update($request->validated());

        return redirect()->route('dashboard')->with('success', 'Availability updated successfully');
    }

    public function destroy($availabilityId)
    {
        $availability = Availability::findOrFail($availabilityId);
        if ($availability->user_id !== Auth::id()) { // TODO -> policy
            return abort(403, 'Unauthorized');
        }
        $availability->delete();
        
        return redirect()->route('dashboard')->with('success', 'Your availability has been successfully removed.');
    }
}
