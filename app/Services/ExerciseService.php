<?php

declare(strict_types=1);

namespace App\Services;

#region Use-Statements
use App\DTO\DataTableQueryParams;
use App\DTO\ExerciseParams;
use App\Entity\Category;
use App\Entity\Exercise;
use App\Entity\TrainingDay;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
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
            $this->entityManager->getRepository(Category::class)->findOneBy(['name' => $data['categoryName']]),
            $data['name'],
            $data['description'],
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
        $exercise->setCategory($params->category);
        $exercise->setDescription($params->description);

        $this->entityManager->persist($exercise);
        $this->entityManager->flush();

        return $exercise;
    }

    public function getPaginatedExercises(DataTableQueryParams $params, int $id): Paginator
    {
        $category = $this->entityManager->find(Category::class, $id);

        $query = $this->entityManager
            ->getRepository(Exercise::class)
            ->createQueryBuilder('e')
            ->leftJoin('e.category', 'c')
            ->where('c = :category')
            ->setParameter('category', $category)
            ->setFirstResult($params->start)
            ->setMaxResults($params->length);

        return $this->sort($query, $params);
    }

    public function getAllPaginatedEXercises(DataTableQueryParams $params): Paginator
    {
        $query = $this->entityManager
            ->getRepository(Exercise::class)
            ->createQueryBuilder('e')
            ->setFirstResult($params->start)
            ->setMaxResults($params->length);

        return $this->sort($query, $params);
    }

    private function sort(QueryBuilder $query, DataTableQueryParams $params): Paginator
    {
        $orderBy  = \in_array($params->orderBy, ['exerciseName', 'description']) ? $params->orderBy : 'exerciseName';
        $orderDir = \strtolower($params->orderDir) === 'asc' ? 'asc' : 'desc';

        if (! empty($params->searchTerm)) {
            $query->where('e.name LIKE :name')->setParameter('name', '%' . \addcslashes($params->searchTerm, '%_') . '%');
        }

        $query->orderBy('e.' . $orderBy, $orderDir);

        return new Paginator($query);
    }
}
