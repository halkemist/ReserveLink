<?php

namespace App\Enums;

/**
 * Enum of the week days.
 * 
 * This enum represent the days of the week and the associated numeric values. (0 for sunday, 1 for monday, etc...).
 * 
 * @method string name() Return the formated name of the week day.
 * @method static array names() Return an array with all of the names of the week days.
 */
enum WeekDays: int
{
    case SUNDAY = 0;
    case MONDAY = 1;
    case TUESDAY = 2;
    case WEDNESDAY = 3;
    case THURSDAY = 4;
    case FRIDAY = 5;
    case SATURDAY = 6;

    /**
     * Return the formated name of the week day.
     * 
     * @return string The day name in beautiful format (example: "Monday").
     */
    public function name(): string
    {
        return match($this)
        {
            WeekDays::SUNDAY => 'Sunday',
            WeekDays::MONDAY => 'Monday',
            WeekDays::TUESDAY => 'Tuesday',
            WeekDays::WEDNESDAY => 'Wednesday',
            WeekDays::THURSDAY => 'Thursday',
            WeekDays::FRIDAY => 'Friday',
            WeekDays::SATURDAY => 'Saturday'
        };
    }

    /**
     * Return an array which contains all the names of the week days.
     * 
     * @return array<string> Array of week days names.
     */
    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }
    
}
