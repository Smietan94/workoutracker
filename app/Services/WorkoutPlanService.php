<?php

declare(strict_types=1);

namespace App\Services;

#region Use-Statements
use App\DTO\DataTableQueryParams;
use App\DTO\WorkoutPlanParams;
use App\Entity\TrainingDay;
use App\Entity\User;
use App\Entity\WorkoutPlan;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Pagination\Paginator;
#endregion

class WorkoutPlanService
{
    public function __construct(private readonly EntityManager $entityManager)
    {
    }

    public function create(WorkoutPlanParams $params): WorkoutPlan
    {
        $workoutPlan = new WorkoutPlan();

        $workoutPlan->setUser($params->user);

        $workoutPlan->setName($params->name);
        $workoutPlan->setTrainingsPerWeek($params->trainingsPerWeek);
        $workoutPlan->setNotes($params->notes);

        $this->entityManager->persist($workoutPlan);

        for ($i=0; $i < $params->trainingsPerWeek; $i++) {
            $trainingDay = new TrainingDay();
            $trainingDay->setWorkoutPlan($workoutPlan);
            $this->entityManager->persist($trainingDay);
        }

        $this->entityManager->flush();

        return $workoutPlan;
    }

    public function update(WorkoutPlan $workoutPlan, WorkoutPlanParams $params): WorkoutPlan
    {
        // TODO

        return $workoutPlan;
    }

    public function getWorkoutPlanParams(array $data, User $user): WorkoutPlanParams
    {
        return new WorkoutPlanParams(
            $user,
            $data['name'],
            (int) $data['trainingsPerWeek'],
            $data['notes']
        );
    }

    public function getPaginatedWorkoutPlans(DataTableQueryParams $params): Paginator
    {
        $query = $this->entityManager
            ->getRepository(WorkoutPlan::class)
            ->createQueryBuilder('w')
            ->setFirstResult($params->start)
            ->setMaxResults($params->length);

        $orderBy = \in_array($params->orderBy, ['name', 'notes', 'trainingsPerWeek', 'createdAt']) ? $params->orderBy : 'createdAt';
        $orderDir = \strtolower($params->orderDir) === 'asc' ? 'asc' : 'desc';

        if (! empty($params->searchTerm)) {
            $query->where('w.name LIKE :name')->setParameter('name', '%' . \addcslashes($params->searchTerm, '%_') . '%');
        }

        $query->orderBy('w.' . $orderBy, $orderDir);

        return new Paginator($query);
    }

    public function delete(int $id): void
    {
        $workoutPlan  = $this->entityManager->find(WorkoutPlan::class, $id);
        $trainingDays = $this->entityManager->getRepository(TrainingDay::class)->findBy(['workoutPlan' => $workoutPlan]);

        foreach($trainingDays as $tD) {
            $this->entityManager->remove($tD);
        }

        $this->entityManager->remove($workoutPlan);
        $this->entityManager->flush();
    }
}
