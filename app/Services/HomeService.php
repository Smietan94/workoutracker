<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\ExerciseResult;
use App\Enum\WorkoutPeriod;
use DateTime;
use Doctrine\ORM\EntityManager;

class HomeService
{
    public function __construct(
        private readonly EntityManager $entityManager,
    ) {
    }

    public function getTrainingDayData(array $exercises, int $period): array
    {
        $modifier = WorkoutPeriod::tryFrom($period)->toString();
        $qb = $this->entityManager
        ->createQueryBuilder()
        ->select('e.exerciseName as exerciseName, r.weight as weight, r.date as date')
        ->from(ExerciseResult::class, 'r')
        ->leftJoin('r.exercise', 'e')
        ->where('e IN (:exercises)');

        if (!$modifier) {
            $qb->orderBy('r.date', 'ASC')
                ->addOrderBy('e.id', 'ASC')
                ->setParameter('exercises', $exercises);
        } else {
            $endDate = new DateTime();
            // $endDate = (new DateTime())->modify('+2 month');
            $startDate = (new DateTime())->modify($modifier);
            $qb->andWhere('r.date BETWEEN :startDate AND :endDate')
                ->orderBy('r.date', 'ASC')
                ->addOrderBy('e.id', 'ASC')
                ->setParameters([
                    'exercises' => $exercises,
                    'startDate' => $startDate,
                    'endDate'   => $endDate
                ]);
        }

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
        $series         = \array_reduce(
            $data,
            function ($carry, $item) {
                $name   = $item['exerciseName'];
                $weight = $item['weight'];

                if (!isset($carry[$name])) {
                    $carry[$name] = [
                        'name' => $name,
                        'data' => []
                    ];
                }

                $carry[$name]['data'][] = [\strtotime($item['date']) * 1000 , (float) $weight];

                return $carry;
        });


        if (!empty($series)) {
            $series = \array_values($series);
        }

        return [
            'dates'          => \array_values($dates),
            'exercisesNames' => \array_values($exercisesNames),
            'series'         => $series
        ];
    }

    public function allItemsEmpty(array $array): bool
    {
        foreach ($array as $item) {
            if(!empty($item)) {
                return \false;
            }
        }

        return true;
    }
}