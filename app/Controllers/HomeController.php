<?php

declare(strict_types=1);

namespace App\Controllers;

#region Use-Statements

use App\ResponseFormatter;
use App\Services\HomeService;
use App\Services\TrainingDayService;
use App\Services\WorkoutPlanService;
use App\Services\WorkoutRecordService;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;
#endregion

class HomeController
{
    public function __construct(
        private readonly Twig $twig,
        private readonly WorkoutRecordService $workoutRecordService,
        private readonly HomeService $homeService,
        private readonly WorkoutPlanService $workoutPlanService,
        private readonly TrainingDayService $trainingDayService,
        private readonly ResponseFormatter $responseFormatter
    ) {
    }

    public function index(Request $request, Response $response): Response
    {
        return $this->twig->render($response, 'dashboard.twig');
    }

    public function indexWorkoutPlan(Request $request, Response $response, array $args): Response
    {
        $workoutPlanId         = (int) $args['id'];
        $workoutPlan           = $this->workoutPlanService->getById($workoutPlanId);
        $trainingDays          = $workoutPlan->getTrainingDays()->toArray();
        $trainingDaysExercises = \array_map(fn ($trainingDay) => $trainingDay->getExercises()->toArray(), $trainingDays);
        $lastTrainigDaysData   = \array_map(
            fn ($exercises) => $this->workoutRecordService->getLastTrainingData($exercises), 
            $trainingDaysExercises
        );
        // $this->homeService->formatTrainingDayData($this->homeService->getTrainingDayData($trainingDaysExercises[0]));

        $data =[
            'lastTrainingsData' => $lastTrainigDaysData,
        ];

        return $this->twig->render(
            $response,
            'dashboard.twig',
            $data
        );
    }

    public function load(Request $request, Response $response, array $args): Response
    {
        $workoutPlanId = (int) $args['id'];
        $workoutPlan   = $this->workoutPlanService->getById($workoutPlanId);
        $trainingDays  = $workoutPlan->getTrainingDays()->toArray();
        $exercises     = $trainingDays[0]->getExercises()->toArray();

        $data         = $this->homeService->getTrainingDayData($exercises);
        $formatedData = $this->homeService->formatTrainingDayData($data);

        return $this->responseFormatter->asChart($response, $formatedData);
    }
}