<?php

declare(strict_types=1);

namespace App\Controllers;

#region Use-Statemsnts
use App\Contracts\RequestValidatorFactoryInterface;
use App\Entity\WorkoutPlan;
use App\RequestValidators\RegisterWorkoutPlanValidator;
use App\ResponseFormatter;
use App\Services\RequestService;
use App\Services\WorkoutPlanService;
use Slim\Views\Twig;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
#endregion

class WorkoutPlansController
{
    public function __construct(
        private readonly Twig $twig,
        private readonly RequestValidatorFactoryInterface $requestValidatorFactory,
        private readonly WorkoutPlanService $workoutPlanService,
        private readonly ResponseFormatter $responseFormatter, 
        private readonly RequestService $requestService
    ){
    }

    public function index(Request $request, Response $response): Response
    {
        return $this->twig->render(
            $response,
            'workouts/index.twig'
        );
    }

    public function store(Request $request, Response $response): Response
    {
        // TODO
        $data = $this->requestValidatorFactory->make(RegisterWorkoutPlanValidator::class)->validate(
            $request->getParsedBody()
        );

        $params = $this->workoutPlanService->getWorkoutPlanParams($data, $request->getAttribute('user'));

        $this->workoutPlanService->create($params);

        return $response;
    }

    public function delete(Request $request, Response $response, array $args): Response
    {
        $this->workoutPlanService->delete((int) $args['id']);

        return $response;
    }

    public function load(Request $request, Response $response): Response
    {
        $params = $this->requestService->getDataTableQueryParams($request);
        $workoutPlans = $this->workoutPlanService->getPaginatedWorkoutPlans($params);

        $transformer = function (WorkoutPlan $workoutPlan) {
            return [
                'id'               => $workoutPlan->getId(),
                'name'             => $workoutPlan->getName(),
                'trainingsPerWeek' => $workoutPlan->getTrainingsPerWeek(),
                'notes'            => $workoutPlan->getNotes(),
                'createdAt'        => $workoutPlan->getCreatedAt()->format('d/m/Y g:i A')
            ];
        };

        $total = \count($workoutPlans);

        return $this->responseFormatter->asDataTable(
            $response,
            \array_map($transformer, (array) $workoutPlans->getIterator()),
            $params->draw,
            $total,
            $total
        );
    }
}
