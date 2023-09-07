<?php

declare(strict_types=1);

namespace App\Enum;

enum WorkoutPeriod: int
{
    case MINUS_1_MONTH  = 0;
    case MINUS_3_MONTH  = 1;
    case MINUS_6_MONTH  = 2;
    case MINUS_1_YEAR   = 3;
    case FROM_BEGINNING = 4;

    public function toString()
    {
        return match($this) {
            self::MINUS_3_MONTH  => '-3 month',
            self::MINUS_6_MONTH  => '-6 month',
            self::MINUS_1_YEAR   => '-1 year',
            self::FROM_BEGINNING => null,
            default              => '-1 month'
        };
    }
}

