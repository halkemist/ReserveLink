<?php

namespace App\Mail;

use App\Models\Booking;
use App\Models\User;
use App\Services\iCalService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        protected Booking $booking
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Booking Confirmation',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.bookings.confirmation',
            with: [
                'ownerId' => $this->booking->owner_id,
                'startTime' => $this->booking->start_time,
                'endTime' => $this->booking->end_time,
                'status' => $this->booking->status,
                'meetLink' => $this->booking->meet_link,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $icsComponent = iCalService::generateIcsFile(
            'Meeting with '.$this->booking->guest_email,
            $this->booking->meet_link,
            User::findOrFail($this->booking->owner_id),
            $this->booking->start_time,
            $this->booking->end_time
        );

        $icsContent = (string) $icsComponent;

        return [
            Attachment::fromData(fn () => $icsContent, 'meeting.ics')
                ->withMime('text/calendar; charset=utf-8'),
        ];
    }
}
