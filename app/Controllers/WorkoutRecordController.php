<?php

declare(strict_types=1);

namespace App\Controllers;

#region Use-Statements

use App\Contracts\RequestValidatorFactoryInterface;
use App\RequestValidators\RegisterTrainingDaySummaryRequestValidator;
use App\ResponseFormatter;
use App\Services\ExerciseService;
use App\Services\RequestService;
use App\Services\WorkoutRecordService;
use DateTime;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;

#endregion

class WorkoutRecordController
{
    public function __construct(
        private readonly Twig $twig,
        private readonly RequestValidatorFactoryInterface $requestValidatorFactory,
        private readonly ResponseFormatter $responseFormatter,
        private readonly RequestService $requestService,
        private readonly WorkoutRecordService $workoutRecordService,
        private readonly ExerciseService $exerciseService
    ) {
    }

    public function index(Request $request, Response $response, array $args): Response
    {
        $trainingDayId = (int) $args['id'];
        // \var_dump($this->workoutRecordService->getTrainingDayData($trainingDayId));
        $data = [
            'trainingDayId' => $trainingDayId,
            'exercises'     => $this->workoutRecordService->getTrainingDayData($trainingDayId),
        ];

        return $this->twig->render(
            $response,
            'workoutRecord/index.twig',
            $data
        );
    }

    public function exercisesSummary(Request $request, Response $response, array $args): Response
    {
        $data = $this->requestValidatorFactory->make(RegisterTrainingDaySummaryRequestValidator::class)->validate(
            $request->getParsedBody()
        );

        $date = DateTime::createFromFormat('Y-m-d', $data['trainingDayDate']);

        foreach ($data['exercises'] as $exerciseData) {
            $exerciseSummaryParams = $this->workoutRecordService->getExerciseSummaryParamsDTO($exerciseData, $date);
            $this->workoutRecordService->recordExercise($exerciseSummaryParams);
        }

        $this->workoutRecordService->recordTrainingDay((int) $data['trainingDayId'], $date, $data['trainingDayNotes']);

        return $response->withHeader('Location', '/workoutplans');
    }
}