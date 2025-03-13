<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookingRequest;
use App\Mail\BookingConfirmation;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class BookingController extends Controller
{
    public function store(BookingRequest $request)
    {
        $authUserId = Auth::id();
        $validated = $request->validated();

        // Check if a booking on the selected time slot exist
        if (Booking::where('owner_id', $validated['owner_id'])
            ->where('status', '!=', 'canceled')
            ->where(function ($q) use ($validated) {
            $q->where(function($q2) use ($validated) {
                $q2->where('start_time', '<', $validated['end_time'])
                      ->where('end_time', '>', $validated['start_time']);
            })->orWhere(function($q2) use ($validated) {
                $q2->where('start_time', '=', $validated['start_time'])
                      ->where('end_time', '=', $validated['end_time']);
            });
        })->exists()
            ) {
            abort(403, 'An appointment has already been scheduled for this slot');
        }

        // In case of an connected user
        if (!isset($validated['user_id']) && $authUserId) {
            $validated['user_id'] = $authUserId;
        }

        $booking = Booking::create($validated);

        Mail::to('test@gmail.com')->send(new BookingConfirmation($booking));

        return redirect()->route('booking.confirmation', $booking->id)->with('success', 'Your booking has been successfully created.');
    }

    public function confirmation(Booking $booking)
    {
        if (Auth::id() !== $booking->user_id && Auth::id() !== $booking->owner_id) {
            abort(403, 'Unauthorized');
        }
        return view('bookings.confirmation', compact('booking'));
    }

    public function cancel(Booking $booking)
    {
        if(Auth::id() !== $booking->owner_id) {
            abort(403, 'Unauthorized');
        }
        
        $booking->status = 'canceled';
        $booking->save();

        return redirect()->route('booking.confirmation', $booking->id)->with('success', 'Booking has been canceled.');
    }
}
