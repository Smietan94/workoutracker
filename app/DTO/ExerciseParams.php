<?php

declare(strict_types=1);

namespace App\DTO;

use App\Entity\Category;
use App\Entity\WorkoutPlan;

class ExerciseParams
{
    public function __construct(
        public readonly WorkoutPlan $workoutPlan,
        public readonly Category $category = null,
        public readonly string $name,
        public readonly int $setsNumber,
        public readonly array $sets,
    )
    {
    }
}
