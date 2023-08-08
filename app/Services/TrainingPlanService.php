<?php

declare(strict_types=1);

namespace App\Services;

#region Use-Statements
use App\DTO\TrainingPlanParams;
use App\Entity\WorkoutPlan;
#endregion 

class TrainingPlanService
{
    public function __construct(
        private readonly WorkoutPlanService $workoutPlanService,
    ) {
    }

    public function getTrainingPlanParams(WorkoutPlan $workoutPlan): TrainingPlanParams
    {
        $trainingDaysArray = (array) $workoutPlan->getTrainingDays()->getIterator();
        $data = $this->getTrainingDaysDataArray($trainingDaysArray);

        return new TrainingPlanParams(
            $workoutPlan->getName(),
            $workoutPlan->getTrainingsPerWeek(),
            $data
        );
    }

    private function getTrainingDaysDataArray(array $trainingDaysArray): array
    {
        $data = [];

        foreach($trainingDaysArray as $trainingDay) {
            $exercisesArray = (array) $trainingDay->getExercises()->getIterator();

            $trainingDayData = [
                'id'            => $trainingDay->getId(),
                'workoutPlanId' => $trainingDay->getWorkoutPlan()->getId(),
                'exercises'     => $this->getExercisesDataArray($exercisesArray),
            ];
            \array_push($data, $trainingDayData);
        }

        return $data;
    }

    private function getExercisesDataArray(array $exercisesArray): array
    {
        $exercises = [];

        foreach($exercisesArray as $exercise) {
            $setsArray = (array) $exercise->getSets()->getIterator();

            $exerciseData = [
                'id'            => $exercise->getId(),
                'categoryId'    => $exercise->getCategory()->getId(),
                'categoryName'  => $exercise->getCategory()->getName(),
                'trainingDayId' => $exercise->getTrainingDay()->getId(),
                'exerciseName'  => $exercise->getExerciseName(),
                'description'   => $exercise->getDescription(),
                'createdAt'     => $exercise->getCreatedAt()->format('d/m/Y g:i A'),
                'updatedAt'     => $exercise->getUpdatedAt()->format('d/m/Y g:i A'),
                'setsNumber'    => count($setsArray),
                'sets'          => $this->getSetsDataArray($setsArray),
            ];
            \array_push($exercises, $exerciseData);
        }

        return $exercises;
    }

    private function getSetsDataArray(array $setsArray): array
    {
        $sets = [];

        foreach ($setsArray as $set) {
            $setData = [
                'id'         => $set->getId(),
                'setNumber'  => $set->getSetNumber(),
                'reps'       => $set->getReps(),
                'exerciseId' => $set->getExercise()->getId(),
                'createdAt'  => $set->getCreatedAt()->format('d/m/Y g:i A'),
                'updatedAt'  => $set->getUpdatedAt()->format('d/m/Y g:i A'),
            ];
            \array_push($sets, $setData);
        }

        return $sets;
    }
}