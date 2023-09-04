<?php

declare(strict_types=1);

namespace App\Services;

#region Use-Statements
use App\DTO\ExerciseParams;
use App\DTO\TrainingPlanParams;
use App\Entity\Category;
use App\Entity\Exercise;
use App\Entity\TrainingDay;
use App\Entity\WorkoutPlan;
use Doctrine\ORM\EntityManager;
#endregion 

class TrainingPlanService
{
    public function __construct(
        private readonly WorkoutPlanService $workoutPlanService,
        private readonly CategoryService $categoryService,
        private readonly SetService $setService,
        private readonly ExerciseService $exerciseService,
        private readonly EntityManager $entityManager
    ) {
    }

    public function getTrainingPlanData(int $workoutPlanId): array
    {
        $workoutPlan    = $this->workoutPlanService->getById($workoutPlanId);
        $trainingParams = $this->getTrainingPlanParams($workoutPlan);

        $data = [
            'workoutName'      => $trainingParams->workoutName,
            'trainingsPerWeek' => $trainingParams->trainingsPerWeek,
            'workoutPlanId'    => $workoutPlan->getId(),
            'data'             => $trainingParams->data,
        ];

        return $data;
    }

    public function update(array $data): void
    {
        $workoutPlan = $this->workoutPlanService->getById((int) $data['workoutPlanId']);

        $trainingDays = (array) $workoutPlan->getTrainingDays()->getIterator();

        for ($i = 0; $i < \count($data['trainingDays']); $i++) {
            $exercises = $this->getExercisesDTOArray(
                $data['trainingDays'][$i]['exercises'], 
                (int) $data['trainingDays'][$i]['trainingDayId']
            );
            $this->processTrainingDay($trainingDays[$i], $exercises);
        }
    }

    private function getExercisesDTOArray(array $exercises, int $trainingDayId): array
    {
        $exercisesDTOArray = [];
        foreach ($exercises as $exercise) {
            if (isset($exercise['exerciseId'])) {
                \array_push(
                    $exercisesDTOArray,
                    new ExerciseParams(
                        $trainingDayId,
                        $exercise['category'],
                        $exercise['exerciseName'],
                        $exercise['description'],
                        \count($exercise['sets']),
                        $exercise['sets'], 
                        (int) $exercise['exerciseId']
                    )
                );
            } else {
                \array_push(
                    $exercisesDTOArray,
                    new ExerciseParams(
                        $trainingDayId,
                        $exercise['category'],
                        $exercise['exerciseName'],
                        $exercise['description'],
                        \count($exercise['sets']),
                        $exercise['sets'], 
                        null
                    )
                );
            }
        }

        return $exercisesDTOArray;
    }

    private function processTrainingDay(TrainingDay $trainingDay, array $exercises): void
    {
        $exercisesToUpdate = (array) $trainingDay->getExercises()->getIterator();

        if (\count($exercises) > \count($exercisesToUpdate)) {
            $newExercises = $this->getNewExercises($exercises);
            foreach($newExercises as $exercise) {
                $this->exerciseService->storeExercise($exercise);
            }
        } elseif (\count($exercises) < \count($exercisesToUpdate)) {
            $exercisesToRemove = $this->getExercisesToRemove($exercises, $exercisesToUpdate);
            $this->exerciseService->removeExercises($exercisesToRemove);
        }

        $exercisesToUpdate = (array) $trainingDay->getExercises()->getIterator();

        for ($i = 0; $i < \count($exercisesToUpdate); $i++) {
            $this->processExercise($exercisesToUpdate[$i], $exercises[$i]);
        }
    }

    private function getExercisesToRemove(array $exercises, array $exercisesFromDB): array{
        $exercisesIdsToRemove        = [];
        $obtainedExercisesIdsToCheck = \array_map(fn($exercise)=>$exercise->id, $exercises);

        foreach ($exercisesFromDB as $exercise) {
            $exerciseId = $exercise->getId();
            if (! \in_array($exerciseId, $obtainedExercisesIdsToCheck)) {
                \array_push($exercisesIdsToRemove, $exerciseId);
            }
        }

        return $exercisesIdsToRemove;
    }

    private function getNewExercises(array $exercises): array
    {
        $newExercises = [];

        foreach($exercises as $exercise) {
            if ($exercise->id === null) {
                \array_push($newExercises, $exercise);
            }
        }

        return $newExercises;
    }

    private function processExercise(Exercise $exercise, ExerciseParams $exerciseParams): Exercise 
    {
        $sets = (array) $exercise->getSets()->getIterator();

        if ($exercise->getExerciseName() !== $exerciseParams->name) {
            $exercise->setExerciseName($exerciseParams->name);
        }

        if ($exercise->getDescription() !== $exerciseParams->description) {
            $exercise->setDescription($exerciseParams->description);
        }

        if ($exercise->getCategory()->getName() !== $exerciseParams->category) {
            $category = $this->processCategory($exerciseParams->category);
            $exercise->setCategory($category);
        }

        if ($exerciseParams->setsNumber > \count($sets)) {
            $setsToAdd = \array_slice($exerciseParams->sets, \count($sets));
            $paramsToAdd = new ExerciseParams(
                $exerciseParams->trainingDay,
                $exerciseParams->category,
                $exerciseParams->name,
                $exerciseParams->description,
                \count($setsToAdd),
                $setsToAdd
            );
            $this->setService->storeSets($paramsToAdd, $exercise);

            foreach ($sets as $index => $set) {
                $repsList = $exerciseParams->sets;
                if ($set->getReps() !== (int) $repsList['set'.$index]) {
                    $set->setReps((int) $repsList['set'.$index]);
                    $this->entityManager->persist($set);
                }
            }
        } elseif ($exerciseParams->setsNumber < count($sets)) {
            $setsToRemove = \array_slice($sets, \count($exerciseParams->sets));
            foreach ($setsToRemove as $setToRemove) {
                $this->entityManager->remove($setToRemove);
            }
        }

        $this->entityManager->persist($exercise);
        $this->entityManager->flush();

        return $exercise;
    }

    private function processCategory(string $categoryName): Category
    {
        $category = $this->categoryService->getByName($categoryName);

        return $category ?? $this->categoryService->create($categoryName);
    }

    private function getTrainingPlanParams(WorkoutPlan $workoutPlan): TrainingPlanParams
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