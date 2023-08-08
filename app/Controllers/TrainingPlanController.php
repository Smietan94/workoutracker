<?php

declare(strict_types=1);

namespace App\Controllers;

#region Use-Statements
use App\Contracts\RequestValidatorFactoryInterface;
use App\ResponseFormatter;
use App\Services\RequestService;
use App\Services\TrainingPlanService;
use App\Services\WorkoutPlanService;
use Slim\Views\Twig;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
#endregion

class TrainingPlanController
{
    public function __construct(
        private readonly Twig $twig,
        private readonly RequestValidatorFactoryInterface $requestValidator,
        private readonly ResponseFormatter $responseFormatter,
        private readonly RequestService $requestService,
        private readonly WorkoutPlanService $workoutPlanService,
        private readonly TrainingPlanService $trainingPlanService,
    ) {
    }

    public function index(Request $request, Response $response, array $args): Response
    {
        $workoutPlanId  = (int) $args['id'];
        $workoutPlan    = $this->workoutPlanService->getById($workoutPlanId);
        $trainingParams = $this->trainingPlanService->getTrainingPlanParams($workoutPlan);

        echo '<pre>';

        echo '</pre>';
        $data = [
            'workoutName'      => $trainingParams->workoutName,
            'trainingsPerWeek' => $trainingParams->trainingsPerWeek,
            'data'             => $trainingParams->data,
        ];
        
        return $this->twig->render(
            $response,
            'trainingPlan/index.twig',
            $data
        );
    }

    public function load(Request $request, Response $response): Response
    {
        return $response;
    }
}