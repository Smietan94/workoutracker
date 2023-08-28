<?php

declare(strict_types=1);

namespace App\Services;

#region Use-Statements

use App\Contracts\SessionInterface;
use App\DTO\DataTableQueryParams;
use App\DTO\ExerciseParams;
use App\Entity\Category;
use App\Entity\Exercise;
use App\Entity\TrainingDay;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use InvalidArgumentException;

#endregion

class ExerciseService
{
    public function __construct(
        private readonly EntityManager $entityManager,
        private readonly TrainingDayService $trainingDayService,
        private readonly CategoryService $categoryService,
        private readonly SetService $setService,
        private readonly SessionInterface $session,
    ) {
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
            $this->entityManager->getRepository(Category::class)->findOneBy(['name' => $data['categoryName']]),
            $data['name'],
            $data['description'],
            \count($sets),
            $sets
        );
    }

    public function getById(int $id): Exercise
    {
        return $this->entityManager->find(Exercise::class, $id);
    }

    public function removeExercises(array $exercisesIds): void
    {
        $exercises = \array_map(fn($exerciseId) => $this->getById($exerciseId), $exercisesIds);
        foreach($exercises as $exercise) {
            $this->entityManager->remove($exercise);
        }
        $this->entityManager->flush();
    }

    public function storeExercise(ExerciseParams $params): Exercise
    {
        $userId   = $this->session->get('user');
        $user     = $this->entityManager->find(User::class, $userId);
        $exercise = new Exercise();

        $exercise->setExerciseName($params->name);
        $exercise->setSetsNumber($params->setsNumber);
        $exercise->setTrainingDay($this->checkTrainingDay($params->trainingDay));
        $exercise->setCategory($this->checkCategory($params->category));
        $exercise->setDescription($params->description);
        $exercise->setUser($user);

        $this->entityManager->persist($exercise);
        $this->entityManager->flush();

        $this->setService->storeSets($params, $exercise);

        return $exercise;
    }

    public function getPaginatedExercises(DataTableQueryParams $params, int $id): Paginator
    {
        $userId   = $this->session->get('user');
        $user     = $this->entityManager->find(User::class, $userId);
        $category = $this->entityManager->find(Category::class, $id);

        $query = $this->entityManager
            ->getRepository(Exercise::class)
            ->createQueryBuilder('e')
            ->leftJoin('e.user', 'u')
            ->leftJoin('e.category', 'c')
            ->where('u = :user')
            ->setParameter('user', $user)
            ->andWhere('c = :category')
            ->setParameter('category', $category)
            ->setFirstResult($params->start)
            ->setMaxResults($params->length);

        return $this->sort($query, $params);
    }

    public function getAllPaginatedEXercises(DataTableQueryParams $params): Paginator
    {
        $userId = $this->session->get('user');
        $user   = $this->entityManager->find(User::class, $userId);

        $query = $this->entityManager
            ->getRepository(Exercise::class)
            ->createQueryBuilder('e')
            ->leftJoin('e.user', 'u')
            ->where('u = :user')
            ->setParameter('user', $user)
            ->setFirstResult($params->start)
            ->setMaxResults($params->length);

        return $this->sort($query, $params);
    }

    private function checkCategory(mixed $category): Category
    {
        if (\is_string($category)) {
            $newCategory = $this->categoryService->getByName($category);
            if (!$newCategory) {
                $newCategory = $this->categoryService->create($category);
            }
            return $newCategory;
        } else if ($category instanceof Category) {
            return $category;
        } else {
            throw new InvalidArgumentException($category . " is not instance of string or Category");
        }
    }

    private function checkTrainingDay(mixed $trainingDay): TrainingDay
    {
        if (\is_int($trainingDay)) {
            return $this->trainingDayService->getById($trainingDay);
        } else if ($trainingDay instanceof TrainingDay) {
            return $trainingDay;
        } else {
            throw new InvalidArgumentException($trainingDay . " is not instance of integer or TrainingDay");
        }
    }

    private function sort(QueryBuilder $query, DataTableQueryParams $params): Paginator
    {
        $orderBy  = \in_array($params->orderBy, ['exerciseName', 'description']) ? $params->orderBy : 'exerciseName';
        $orderDir = \strtolower($params->orderDir) === 'asc' ? 'asc' : 'desc';

        if (! empty($params->searchTerm)) {
            $query->where('e.exerciseName LIKE :exerciseName')->setParameter('exerciseName', '%' . \addcslashes($params->searchTerm, '%_') . '%');
        }

        $query->orderBy('e.' . $orderBy, $orderDir);

        return new Paginator($query);
    }
}
