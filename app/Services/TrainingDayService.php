<?php

declare(strict_types=1);

namespace App\Services;

#region Use-Statement
use App\Entity\TrainingDay;
use App\Entity\WorkoutPlan;
use Doctrine\ORM\EntityManager;
#endregion

class TrainingDayService
{
    public function __construct(private readonly EntityManager $entityManager)
    {
    }

    public function create(WorkoutPlan $workoutPlan): TrainingDay
    {
        $trainingDay = new TrainingDay();
        $trainingDay->setWorkoutPlan($workoutPlan);

        $this->entityManager->persist($trainingDay);
        $this->entityManager->flush();

        return $trainingDay;
    }

    public function getById(int $id): TrainingDay
    {
        return $this->entityManager->find(TrainingDay::class, $id);
    }
}
