<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookingRequest;
use App\Mail\BookingCancelation;
use App\Mail\BookingConfirmation;
use App\Models\Booking;
use App\Models\User;
use App\Services\JitsiMeetService;
use Illuminate\Support\Facades\Mail;

/**
 * Controller for managing booking appointments.
 * 
 * Handle the create, update and delete of appointments.
 */
class BookingController extends Controller
{
    /**
     * Jitsi Meet integration service.
     * 
     * @var JitsiMeetService
     */
    private $jitsiService;

    /**
     * Create a new instance.
     * 
     * @param JitsiMeetService $jitsiService Service for Jitsi Meet integration.
     */
    public function __construct(JitsiMeetService $jitsiService)
    {
        $this->jitsiService = $jitsiService;
    }

    /**
     * Store a new booking appointment.
     * 
     * Validate request data, check scheduling conflitcs, create the booking record, generate a meet link and send confirmation emails.
     * 
     * @param BookingRequest $request Validated booking request.
     * @return RedirectResponse Redirect to booking confirmation view.
     * @throws HttpException If slot already booked.
     */
    public function store(BookingRequest $request)
    {
        $validated = $request->validated();

        // Check if a booking on the selected time slot exist
        $slotExists = Booking::where('owner_id', $validated['owner_id'])
            ->where('status', '!=', 'canceled')
            ->where(function ($q) use ($validated) {
                $q->where(function($q2) use ($validated) {
                    $q2->where('start_time', '<', $validated['end_time'])
                        ->where('end_time', '>', $validated['start_time']);
                })->orWhere(function($q2) use ($validated) {
                    $q2->where('start_time', '=', $validated['start_time'])
                        ->where('end_time', '=', $validated['end_time']);
                });
            })->exists(); // TODO -> in a repository/model
        
        abort_if($slotExists, 403, "An appointment has already been scheduled for this slot"); // TODO -> create a policy

        // Store the new booking entry
        $booking = Booking::create($validated);

        // Create and store the meeting link
        $this->jitsiService->createMeeting($booking); // TODO -> turn into repository / generic service

        // Update nbooking instance to get the last meeting added link
        $booking->refresh();

        // Send confirmation mail
        Mail::to($validated['guest_email'])->send(new BookingConfirmation($booking)); // TODO -> put in a job

        return redirect()->route('booking.confirmation', $booking->id)->with('success', 'Your booking has been successfully created.');
    }

    /**
     * Display booking confirmation details.
     * 
     * Show details of a confirmed booking (time, date, attendees, meeting link).
     * 
     * @param Booking $booking Booking to display.
     * @return View Confirmation view with booking details.
     */
    public function confirmation(Booking $booking)
    {
        return view('bookings.confirmation', compact('booking'));
    }

    /**
     * Cancel an existing appointment.
     * 
     * Update the booking status to canceled and send an email to both the guest and owner.
     * 
     * @param Booking $booking Booking to cancel.
     * @return RedirectResponse Redirect to booking confirmation view with status.
     * @throws AuthorizationException If user not authorized to cancel.
     */
    public function cancel(Booking $booking)
    {
        $this->authorize('cancel', $booking);
        
        $booking->status = 'canceled';
        $booking->save();

        // Send confirmation mails
        Mail::to($booking->guest_email)->send(new BookingCancelation($booking, false));
        $ownerEmail = User::find($booking->owner_id)->first()->pluck('email');
        if ($ownerEmail) {
            Mail::to($ownerEmail)->send(new BookingCancelation($booking, true)); // TODO -> put in a job
        }

        return redirect()->route('booking.confirmation', $booking->id)->with('success', 'Booking has been canceled.');
    }
}
