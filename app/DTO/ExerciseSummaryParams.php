<?php

declare(strict_types=1);

namespace App\DTO;

use App\Entity\Exercise;
use DateTime;

class ExerciseSummaryParams
{
    public function __construct(
        public readonly Exercise $exercise,
        public readonly int $weight,
        public readonly DateTime $date,
        public readonly string $notes = '',
    ) {
    }
}