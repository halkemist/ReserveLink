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

class iCalService 
{
  /**
   * Generate the ICS file
   */
  public static function generateIcsFile(
    string $title,
    string $description,
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
    $event->setDescription($description);
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