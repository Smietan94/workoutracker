<?php

declare(strict_types=1);

namespace App\Services;

#region Use-Statements

use App\Contracts\SessionInterface;
use App\DTO\DataTableQueryParams;
use App\DTO\WorkoutPlanParams;
use App\Entity\User;
use App\Entity\WorkoutPlan;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Pagination\Paginator;
#endregion

class WorkoutPlanService
{
    public function __construct(
        private readonly EntityManager $entityManager,
        private readonly SessionInterface $session,
    ) {
    }

    public function create(WorkoutPlanParams $params): WorkoutPlan
    {
        $workoutPlan = new WorkoutPlan();

        $workoutPlan->setUser($params->user);

        $workoutPlan->setName($params->name);
        $workoutPlan->setTrainingsPerWeek($params->trainingsPerWeek);
        $workoutPlan->setNotes($params->notes);

        $this->entityManager->persist($workoutPlan);

        $this->entityManager->flush();

        return $workoutPlan;
    }

    public function update(WorkoutPlan $workoutPlan, WorkoutPlanParams $params): WorkoutPlan
    {
        if ($workoutPlan->getName() !== $params->name) {
            $workoutPlan->setName($params->name);
        }

        if ($workoutPlan->getNotes() !== $params->notes) {
            $workoutPlan->setNotes($params->notes);
        }

        $this->entityManager->persist($workoutPlan);
        $this->entityManager->flush();

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
        $userId = $this->session->get('user');
        $user   = $this->entityManager->find(User::class, $userId);
        $query  = $this->entityManager
            ->getRepository(WorkoutPlan::class)
            ->createQueryBuilder('w')
            ->leftJoin('w.user', 'u')
            ->where('u = :user')
            ->setParameter('user', $user)
            ->setFirstResult($params->start)
            ->setMaxResults($params->length);

        $orderBy  = \in_array($params->orderBy, ['name', 'notes', 'trainingsPerWeek', 'createdAt']) ? $params->orderBy : 'createdAt';
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

        if (! $workoutPlan) {
            throw new \InvalidArgumentException("Workout Plan with ID " . $id . " does not exists.");
        }

        $this->entityManager->remove($workoutPlan);
        $this->entityManager->flush();
    }

    public function getById(int $id): WorkoutPlan
    {
        return $this->entityManager->find(WorkoutPlan::class, $id);
    }
}
