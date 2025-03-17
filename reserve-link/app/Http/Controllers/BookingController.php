<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookingRequest;
use App\Mail\BookingCancelation;
use App\Mail\BookingConfirmation;
use App\Models\Booking;
use App\Models\User;
use App\Services\JitsiMeetService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;

class BookingController extends Controller
{
    private $jitsiService;

    public function __construct(JitsiMeetService $jitsiService)
    {
        $this->jitsiService = $jitsiService;
    }

    public function store(BookingRequest $request)
    {
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
        })->exists() // TODO -> in a policy
            ) {
            abort(403, 'An appointment has already been scheduled for this slot');
        }

        // Store the new booking entry
        $booking = Booking::create($validated);

        // Create and store the meeting link
        $this->jitsiService->createMeeting($booking); // TODO -> turn into repository / generic service

        // Update nbooking instance to get the last meeting added link
        $booking->refresh();

        // Send confirmation mail
        Mail::to($validated['guest_email'])->send(new BookingConfirmation($booking));

        return redirect()->route('booking.confirmation', $booking->id)->with('success', 'Your booking has been successfully created.');
    }

    public function confirmation(Booking $booking)
    {
        return view('bookings.confirmation', compact('booking'));
    }

    public function cancel(Booking $booking)
    {
        $this->authorize('cancel', $booking);
        
        $booking->status = 'canceled';
        $booking->save();

        // Send confirmation mails
        Mail::to($booking->guest_email)->send(new BookingCancelation($booking, false));
        $ownerEmail = User::find($booking->owner_id)->first()->pluck('email');
        if ($ownerEmail) {
            Mail::to($ownerEmail)->send(new BookingCancelation($booking, true));
        }

        return redirect()->route('booking.confirmation', $booking->id)->with('success', 'Booking has been canceled.');
    }
}
