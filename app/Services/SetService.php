<?php

declare(strict_types=1);

namespace App\Services;

#region Use-Statements
use App\DTO\ExerciseParams;
use App\Entity\Exercise;
use App\Entity\Set;
use Doctrine\ORM\EntityManager;
#endregion

class SetService
{
    public function __construct(private readonly EntityManager $entityManager)
    {
    }

    public function storeSets(ExerciseParams $params, Exercise $exercise): void
    {
        $setNumber = 0;
        foreach($params->sets as $set) {
            $newSet = new Set();

            $newSet->setExercise($exercise);
            $newSet->setReps((int) $set);
            $newSet->setSetNumber($setNumber++);

            $this->entityManager->persist($newSet);
        }

        $this->entityManager->flush();

    }
}
