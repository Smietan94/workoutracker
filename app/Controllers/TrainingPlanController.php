<?php

declare(strict_types=1);

namespace App\Controllers;

#region Use-Statements
use App\Contracts\RequestValidatorFactoryInterface;
use App\RequestValidators\UpdateTrainingPlanRequestValidator;
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
        private readonly RequestValidatorFactoryInterface $requestValidatorFactory,
        private readonly ResponseFormatter $responseFormatter,
        private readonly RequestService $requestService,
        private readonly WorkoutPlanService $workoutPlanService,
        private readonly TrainingPlanService $trainingPlanService,
    ) {
    }

    public function index(Request $request, Response $response, array $args): Response
    {
        $workoutPlanId  = (int) $args['id'];
        $data           = $this->trainingPlanService->getTrainingPlanData($workoutPlanId);
        
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

    public function editTrainingPlan(Request $request, Response $response, array $args): Response
    {
        $workoutPlanId = (int) $args['id'];
        $data          = $this->trainingPlanService->getTrainingPlanData($workoutPlanId);

        return $this->twig->render(
            $response,
            'trainingPlan/edit.twig',
            $data
        );
    }

    public function updateTrainingPlan(Request $request, Response $response, array $args): Response
    {
        $dataToUpdate = $this->requestValidatorFactory->make(UpdateTrainingPlanRequestValidator::class)->validate(
            $request->getParsedBody()
        );

        echo '<pre style="background:white">';
        \print_r($dataToUpdate);
        echo '</pre>';

        $this->trainingPlanService->update($dataToUpdate);

        $workoutPlanId  = (int) $args['id'];
        $data           = $this->trainingPlanService->getTrainingPlanData($workoutPlanId);

        return $this->twig->render(
            $response,
            'trainingPlan/index.twig',
            $data
        );
    }
}