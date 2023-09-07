<?php

declare(strict_types=1);

namespace App\Services;

#region Use-Statements
use App\Contracts\SessionInterface;
use App\DTO\ExerciseSummaryParams;
use App\Entity\ExerciseResult;
use App\Entity\TrainingDayResult;
use App\Entity\User;
use App\Exception\InvalidUserException;
use DateTime;
use Doctrine\ORM\EntityManager;
#endregion

class WorkoutRecordService
{
    public function __construct(
        private readonly EntityManager $entityManager,
        private readonly SessionInterface $session,
        private readonly TrainingDayService $trainingDayService,
        private readonly ExerciseService $exerciseService
    ) {
    }

    public function getTrainingDayData(int $id): array
    {
        $trainingDay     = $this->trainingDayService->getById($id);
        $trainingDayUser = $trainingDay->getWorkoutPlan()->getUser();
        $currentUser     = $this->entityManager->find(User::class, $this->session->get('user'));

        if ($trainingDayUser !== $currentUser) {
            throw new InvalidUserException("Invalid user, You dont have access to this workout plan");
        }

        $exercises           = $trainingDay->getExercises()->toArray();
        $lastTrainingData = $this->getLastTrainingData($exercises);

        if (! $lastTrainingData) {
            $exercisesData = \array_map(fn ($exercise) => [
                'exerciseName' => $exercise->getExerciseName(),
                'exerciseId'   => $exercise->getId(),
                'sets'         => \array_map(fn ($set) => $set->getReps(), $exercise->getSets()->toArray()),
            ], $exercises);
        } else {
            $exercisesData = $this->processTrainingDayData($exercises, $lastTrainingData);
        }

        return $exercisesData;
    }

    public function getExerciseSummaryParamsDTO(array $exerciseData, DateTime $date): ExerciseSummaryParams
    {
        return new ExerciseSummaryParams(
            $this->exerciseService->getById((int) $exerciseData['exerciseId']),
            (int) $exerciseData['weight'],
            $date,
            $exerciseData['notes'],
        );
    }

    public function recordExercise(ExerciseSummaryParams $params): void
    {
        $exerciseResult = new ExerciseResult();
        $exerciseResult->setExercise($params->exercise);
        $exerciseResult->setWeight($params->weight);
        $exerciseResult->setNotes($params->notes);
        $exerciseResult->setDate($params->date);

        $this->entityManager->persist($exerciseResult);
        $this->entityManager->flush();
    }

    public function recordTrainingDay(int $trainingDayid, DateTime $date, string $notes): void
    {
        $trainingDay       = $this->trainingDayService->getById($trainingDayid);
        $trainingDayResult = new TrainingDayResult();
        $trainingDayResult->setTrainingDay($trainingDay);
        $trainingDayResult->setDate($date);
        $trainingDayResult->setNotes($notes);

        $this->entityManager->persist($trainingDayResult);
        $this->entityManager->flush();
    }

    private function processTrainingDayData(array $exercises, array $weights): array
    {
        $exercisesData = [];
        foreach ($exercises as $index => $exercise) {
            \array_push($exercisesData,[
                'exerciseName' => $exercise->getExerciseName(),
                'exerciseId'   => $exercise->getId(),
                'sets'         => \array_map(fn ($set) => $set->getReps(), $exercise->getSets()->toArray()),
                'weight'       => (float) $weights[$index]['weight']
            ]);
        }

        return $exercisesData;
    }

    public function getLastTrainingData(array $exercises): ?array
    {
        $qb = $this->entityManager
            ->createQueryBuilder()
            ->select('e.exerciseName as exerciseName, r.weight as weight, r.notes as notes, r.date as date')
            ->from(ExerciseResult::class, 'r')
            ->leftJoin('r.exercise', 'e')
            ->where('e IN (:exercises)')
            ->orderBy('r.date', 'DESC')
            ->addOrderBy('e.id', 'ASC')
            ->setMaxResults(\count($exercises))
            ->setParameter('exercises', $exercises);

        $results = ($qb->getQuery()->getResult());

        if (empty($results)) {
            return null;
        }

        return \array_map(fn ($result) => [
            'exerciseName' => $result['exerciseName'],
            'weight'       => $result['weight'],
            'notes'        => $result['notes'],
            'date'         => $result['date']->format('d-m-Y')
        ], $results);
    }
}
