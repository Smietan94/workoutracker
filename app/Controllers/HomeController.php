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
        $workoutPlanId   = (int) $args['id'];
        $workoutPlan     = $this->workoutPlanService->getById($workoutPlanId);
        $user            = $workoutPlan->getUser();
        $workoutPlans    = $user->getWorkoutPlans()->toArray();
        $workoutPlansIds = \array_map(
            fn ($workoutPlan) => [
                'workoutPlanName' => $workoutPlan->getName(),
                'workoutPlanId'   => $workoutPlan->getId()
            ], $workoutPlans);

        // Retrieves exercises
        $trainingDays          = $workoutPlan->getTrainingDays()->toArray();
        $trainingDaysExercises = \array_map(
            fn ($trainingDay) => $trainingDay->getExercises()->toArray(), 
            $trainingDays
        );

        // Retrieves last training results
        $lastTrainigDaysData   = \array_map(
            fn ($exercises) => $this->workoutRecordService->getLastTrainingData($exercises), 
            $trainingDaysExercises
        );

        // Checking if any trainig records are provided
        if ($this->homeService->allItemsEmpty($lastTrainigDaysData)) {
            $data =[
                'lastTrainingsData' => null,
                'workoutPlansData'  => $workoutPlansIds
            ];
        }else {
            $data =[
                'lastTrainingsData' => $lastTrainigDaysData,
                'workoutPlansData'  => $workoutPlansIds,
            ];
        }

        return $this->twig->render(
            $response,
            'dashboard.twig',
            $data
        );
    }

    public function load(Request $request, Response $response, array $args): Response
    {
        $workoutPlanId    = (int) $args['id'];
        $trainingDayIndex = (int) $args['trainingDayIndex'];
        $period           = (int) $args['period'];
        $workoutPlan      = $this->workoutPlanService->getById($workoutPlanId);
        $trainingDays     = $workoutPlan->getTrainingDays()->toArray();

        // if invalid training day provided, returns null
        if(!isset($trainingDays[$trainingDayIndex])) {
            return $this->responseFormatter->asChart($response, [null]);
        }

        $exercises    = $trainingDays[$trainingDayIndex]->getExercises()->toArray();
        $data         = $this->homeService->getTrainingDayData($exercises, $period);
        $formatedData = $this->homeService->formatTrainingDayData($data);

        return $this->responseFormatter->asChart($response, $formatedData);
    }
}