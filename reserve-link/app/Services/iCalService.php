<?php

namespace App\Services;

use App\Models\User;
use DateTimeImmutable;
use Eluceo\iCal\Domain\Entity\Calendar;
use Eluceo\iCal\Domain\Entity\Event;
use Eluceo\iCal\Domain\ValueObject\DateTime;
use Eluceo\iCal\Domain\ValueObject\EmailAddress;
use Eluceo\iCal\Domain\ValueObject\Organizer;
use Eluceo\iCal\Domain\ValueObject\TimeSpan;
use Eluceo\iCal\Domain\ValueObject\UniqueIdentifier;
use Eluceo\iCal\Domain\ValueObject\Uri;
use Eluceo\iCal\Presentation\Component;
use Eluceo\iCal\Presentation\Factory\CalendarFactory;

/**
 * Service for generating iCalendar (ICS) files.
 * 
 * This service provide functionality to create calendar events in the iCalendar format (RFC 5545) for integration with calendar applications.
 */
class iCalService 
{
  /**
   * Generate the ICS file for a calendar event.
   * 
   * Create an iCalendar component containing an event with the specified details. The component is used to generate a downlodable ICS file.
   * 
   * @param string $title Title of the event.
   * @param string $link URL meet link.
   * @param User $owner User who own the event.
   * @param string $startDate Start date and time in 'Y-m-d H:i:s' format.
   * @param string $endDate End date and time in 'Y-m-d H:i:s' format.
   * @return Component iCalendar component.
   */
  public static function generateIcsFile(
    string $title,
    string $link,
    User $owner,
    string $startDate,
    string $endDate
  ): Component {

    // Create event
    $event = new Event(
      new UniqueIdentifier('event-' . uniqid()),
    );
    $event->setOccurrence(
      new TimeSpan(
        new DateTime(DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $startDate), true),
        new DateTime(DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $endDate), true)
      )
    );
    $event->setSummary($title);
    $event->setOrganizer(new Organizer(
      new EmailAddress($owner->email),
      $owner->name
    ));
    $event->setUrl(new Uri($link));

    // Create the calendar
    $calendar = new Calendar([$event]);

    // Generate ICS content
    return new CalendarFactory()->createCalendar($calendar);
  }
}