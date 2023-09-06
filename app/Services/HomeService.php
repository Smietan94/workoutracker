<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\ExerciseResult;
use DateTime;
use Doctrine\ORM\EntityManager;

class HomeService
{
    public function __construct(
        private readonly EntityManager $entityManager,
    ) {
    }

    public function getTrainingDayData(array $exercises): array
    {
        // $endDate = new DateTime();
        $endDate = (new DateTime())->modify('+1 month');
        $startDate = (new DateTime())->modify('-1 month');

        $qb = $this->entityManager
        ->createQueryBuilder()
        ->select('e.exerciseName as exerciseName, r.weight as weight, r.date as date')
        ->from(ExerciseResult::class, 'r')
        ->leftJoin('r.exercise', 'e')
        ->where('e IN (:exercises)')
        ->andWhere('r.date BETWEEN :startDate AND :endDate')
        ->orderBy('r.date', 'DESC')
        ->addOrderBy('e.id', 'ASC')
        ->setParameters([
            'exercises' => $exercises,
            'startDate' => $startDate,
            'endDate'   => $endDate
        ]);

        $results = $qb->getQuery()->getResult();

        return \array_map(fn ($result) => [
            'exerciseName' => $result['exerciseName'],
            'weight'       => $result['weight'],
            'date'         => $result['date']->format('d-m-Y')
        ], $results);
    }

    public function formatTrainingDayData(array $data): array
    {
        $dates          = \array_unique(\array_map(fn ($item) => $item['date'], $data));
        $exercisesNames = \array_unique(\array_map(fn ($item) => $item['exerciseName'], $data));
        $series      = \array_reduce($data, function ($carry, $item) {
            $name   = $item['exerciseName'];
            $weight = $item['weight'];

            if (!isset($carry[$name])) {
                $carry[$name] = [
                    'name' => $name,
                    'data' => []
                ];
            }

            $carry[$name]['data'][] = (float) $weight;

            return $carry;
        });

        return [
            'dates'          => \array_values($dates),
            'exercisesNames' => \array_values($exercisesNames),
            'series'         => \array_values($series)
        ];
    }
}