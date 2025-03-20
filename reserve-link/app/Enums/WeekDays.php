<?php

namespace App\Enums;

enum WeekDays: int
{
    case SUNDAY = 0;
    case MONDAY = 1;
    case TUESDAY = 2;
    case WEDNESDAY = 3;
    case THURSDAY = 4;
    case FRIDAY = 5;
    case SATURDAY = 6;

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

    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }
    
}
