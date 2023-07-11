<?php

declare(strict_types=1);

namespace App\DTO;

use App\Entity\User;

class WorkoutPlanParams
{
    public function __construct(
        public readonly User $user,
        public readonly string $name,
        public readonly int $trainingsPerWeek,
        public readonly string $notes
    ) {
    }
}