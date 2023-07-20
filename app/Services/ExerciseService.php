<?php

declare(strict_types=1);

namespace App\Services;

#region Use-Statements

use App\DTO\ExerciseParams;
use Doctrine\ORM\EntityManager;
#endregion

class ExerciseService
{
    public function __construct(private readonly EntityManager $entityManager)
    {
    }

    // public function getExerciseParams(array $data): ExerciseParams
    // {
        // return new ExerciseParams(

        // );
    // }
}
