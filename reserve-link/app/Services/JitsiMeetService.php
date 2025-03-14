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
    // Create an unique meeting ID
    $meetingId = 'reservelink-' . $booking->id . '-' . Str::random(6);

    // Build the meeting URL
    $meetingUrl = 'https://' . $this->domain . '/' . $meetingId;

    // Save in DB
    // TODO -> new field in booking table
  }

}