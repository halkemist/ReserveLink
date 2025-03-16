<?php

namespace App\Services;

use App\Models\Booking;
use Illuminate\Support\Str;

class JitsiMeetService
{
  /**
   * Default jitsi domain
   */
  protected $domain;

  /**
   * Create a new service instance
   * @link https://jitsi.org/
   * @link https://jitsi.github.io/handbook/docs/dev-guide/dev-guide-iframe
   */
  public function __construct()
  {
    $this->domain = config('services.jitsi.domain', 'meet.jit.si');
  }

  public function createMeeting(Booking $booking)
  {
    $booking->meetingUrl = $this->generateMeetLink($booking->id);
    $booking->save();
  }

  public function generateMeetLink($bookingId)
  {
    // Create an unique meeting ID
    $meetingId = 'reservelink-' . $bookingId . '-' . Str::random(6);

    // Build the meeting URL
    return 'https://' . $this->domain . '/' . $meetingId;
  }

}