<?php

declare(strict_types=1);

namespace App\DTO;

class TrainingPlanParams
{
    public function __construct(
        public readonly string $workoutName,
        public readonly int $trainingsPerWeek,
        public readonly array $data,
    ) {
    }
}

