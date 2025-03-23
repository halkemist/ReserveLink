<?php

namespace App\Services;

use App\Models\Booking;
use Illuminate\Support\Str;

/**
 * Service for Jitsi Meet video conferencing integration.
 * 
 * Handle the generation and management of Jitsi Meet video conference links for booking appointments.
 * 
 * @link https://jitsi.org/ Jitsi Meet website.
 * @link https://jitsi.github.io/handbook/docs/dev-guide/dev-guide-iframe Jitsi API.
 */
class JitsiMeetService
{
  /**
   * Default jitsi domain to use for meeting link generation.
   * 
   * @var string
   */
  protected $domain;

  /**
   * Create a new Jitsi Meet service instance.
   * 
   * Initialize the service with the configured domain or fallback.
   */
  public function __construct()
  {
    $this->domain = config('services.jitsi.domain', 'meet.jit.si');
  }

  /**
   * Create a meeting link save it into the booking record.
   * 
   * @param Booking $booking Booking that neets a meeting link.
   * @return void
   */
  public function createMeeting(Booking $booking): void
  {
    $booking->meet_link = $this->generateMeetLink($booking->id);
    $booking->save();
  }

  /**
   * Generate an unique Jitsi Meet link.
   * 
   * @param int $bookingId Booking id.
   * @return string Complete Jitsi Meet URL.
   */
  public function generateMeetLink($bookingId): string
  {
    // Create an unique meeting ID
    $meetingId = 'reservelink-' . (string) $bookingId . '-' . Str::random(6);

    // Build the meeting URL
    return 'https://' . $this->domain . '/' . $meetingId;
  }

}