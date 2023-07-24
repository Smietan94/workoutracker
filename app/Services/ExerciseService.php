<?php

declare(strict_types=1);

namespace App\Services;

#region Use-Statements
use App\DTO\ExerciseParams;
use App\Entity\Exercise;
use App\Entity\TrainingDay;
use Doctrine\ORM\EntityManager;
#endregion

class ExerciseService
{
    public function __construct(private readonly EntityManager $entityManager)
    {
    }

    public function getExerciseParams(array $data): ExerciseParams
    {
        $sets = [];
        foreach(\array_keys($data) as $key) {
            if (\preg_match('/set\d+/', $key)) {
                \array_push($sets, $data[$key]);
            }
        }
        return new ExerciseParams(
            $this->entityManager->find(TrainingDay::class, $data['trainingDayId']),
            // null,
            $data['name'],
            \count($sets),
            $sets
        );
    }

    public function storeExercise(ExerciseParams $params): Exercise
    {
        $exercise = new Exercise();

        $exercise->setExerciseName($params->name);
        $exercise->setSetsNumber($params->setsNumber);
        $exercise->setTrainingDay($params->trainingDay);

        $this->entityManager->persist($exercise);
        $this->entityManager->flush();

        return $exercise;
    }
}
