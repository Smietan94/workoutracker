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
        private readonly TrainingDayService $trainingDayService
    ) {
    }

    public function create(WorkoutPlanParams $params): WorkoutPlan
    {
        $user = $params->user;
        $workoutPlan = new WorkoutPlan();

        $workoutPlan->setUser($user);

        $workoutPlan->setName($params->name);
        $workoutPlan->setTrainingsPerWeek($params->trainingsPerWeek);
        $workoutPlan->setNotes($params->notes);

        $this->entityManager->persist($workoutPlan);

        $this->entityManager->flush();

        if (\count($user->getWorkoutPlans()->toArray()) <= 1) {
            $user->setMainWorkoutPlanId($workoutPlan->getId());
        }

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
            $query->andWhere('w.name LIKE :name')->setParameter('name', '%' . \addcslashes($params->searchTerm, '%_') . '%');
        }

        $query->orderBy('w.' . $orderBy, $orderDir);

        return new Paginator($query);
    }

    public function delete(int $id): void
    {
        $workoutPlan = $this->getById($id);
        $userId      = $this->session->get('user');
        $user        = $this->entityManager->find(User::class, $userId);

        if (! $workoutPlan) {
            throw new \InvalidArgumentException("Workout Plan with ID " . $id . " does not exists.");
        }

        if ($user->getMainWorkoutPlanId() === $workoutPlan->getId()) {
            $user->setMainWorkoutPlanId(null);
        }

        $this->entityManager->remove($workoutPlan);
        $this->entityManager->flush();
    }

    public function getById(int $id): WorkoutPlan
    {
        return $this->entityManager->find(WorkoutPlan::class, $id);
    }
}
