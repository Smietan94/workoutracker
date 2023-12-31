<?php

declare(strict_types=1);

namespace App\Controllers;

#region Use-Statemsnts
use App\Contracts\RequestValidatorFactoryInterface;
use App\Contracts\SessionInterface;
use App\DTO\WorkoutPlanParams;
use App\Entity\WorkoutPlan;
use App\RequestValidators\RegisterExerciseRequestValidator;
use App\RequestValidators\RegisterWorkoutPlanValidator;
use App\RequestValidators\UpdateWorkoutPlanRequestValidator;
use App\ResponseFormatter;
use App\Services\CategoryService;
use App\Services\ExerciseService;
use App\Services\RequestService;
use App\Services\SetService;
use App\Services\TrainingDayService;
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
        private readonly CategoryService $categoryService,
        private readonly WorkoutPlanService $workoutPlanService,
        private readonly TrainingDayService $trainingDayService,
        private readonly ExerciseService $exerciseService,
        private readonly SetService $setService,
        private readonly ResponseFormatter $responseFormatter, 
        private readonly RequestService $requestService,
        private readonly SessionInterface $session,
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
        $data        = $this->validateRequestData(RegisterWorkoutPlanValidator::class, $request->getParsedBody());
        $params      = $this->workoutPlanService->getWorkoutPlanParams($data, $request->getAttribute('user'));
        $workoutPlan = $this->workoutPlanService->create($params);

        $this->createTrainingDays($params->trainingsPerWeek, $workoutPlan);
        $this->session->put("TRAININGS_PER_WEEK", $params->trainingsPerWeek);
        $this->session->put('CURRENTLY_ADDED_WORKOUT_PLAN_ID', $workoutPlan->getId());

        return $response;
    }

    public function delete(Request $request, Response $response, array $args): Response
    {
        $this->workoutPlanService->delete((int) $args['id']); // deleting workout plan

        return $response;
    }

    public function load(Request $request, Response $response): Response
    {
        $params = $this->requestService->getDataTableQueryParams($request);
        $workoutPlans = $this->workoutPlanService->getPaginatedWorkoutPlans($params);

        // data format pattern
        $transformer = function (WorkoutPlan $workoutPlan) {
            return [
                'id'               => $workoutPlan->getId(),
                'name'             => $workoutPlan->getName(),
                'trainingsPerWeek' => $workoutPlan->getTrainingsPerWeek(),
                'notes'            => $workoutPlan->getNotes(),
                'createdAt'        => $workoutPlan->getCreatedAt()->format('d/m/Y g:i A'),
                'updatedAt'        => $workoutPlan->getUpdatedAt()->format('d/m/Y g:i A'),
            ];
        };

        return $this->responseFormatter->asDataTable(
            $response,
            \array_map($transformer, (array) $workoutPlans->getIterator()),
            $params->draw,
            \count($workoutPlans)
        );
    }

    public function get(Request $request, Response $response, array $args): Response
    {
        $workoutPlan = $this->isWorkoutPlanExist($response, $args);

        $data = [
            'id'    => $workoutPlan->getId(),
            'name'  => $workoutPlan->getName(),
            'notes' => $workoutPlan->getNotes()
        ];

        return $this->responseFormatter->asJson($response, $data);
    }

    public function update(Request $request, Response $response, array $args): Response
    {
        $data = $this->validateRequestData(UpdateWorkoutPlanRequestValidator::class, $args + $request->getParsedBody());

        $workoutPlan = $this->isWorkoutPlanExist($response, $data);
        $params      = $this->workoutPlanService->getWorkoutPlanParams($data, $request->getAttribute('user'));

        $this->workoutPlanService->update($workoutPlan, $params);

        return $response;
    }

    public function addExercise(Request $request, Response $response): Response
    {
        $data = $this->validateRequestData(RegisterExerciseRequestValidator::class, $request->getParsedBody());

        $workoutPlanId = $this->session->get('CURRENTLY_ADDED_WORKOUT_PLAN_ID');
        $workoutPlan   = $this->workoutPlanService->getById($workoutPlanId);
        $trainingDays  = $workoutPlan->getTrainingDays()->getIterator();
        $trainingDay   = $data['trainingDay'];

        $category = $this->categoryService->getByName($data['categoryName']);

        // if category does not exist it creates new one
        if (! $category) {
            $this->categoryService->create($data['categoryName']);
        } 

        $params = $this->exerciseService->getExerciseParams(($data + ['trainingDayId' => $trainingDays[$trainingDay]->getId()]));
        $this->exerciseService->storeExercise($params);

        return $response;
    }

    public function getTrainingsPerWeek(Request $request, Response $response): Response
    {
        $data = [
            'trainingsPerWeek' => $this->session->get('TRAININGS_PER_WEEK')
        ];

        return $this->responseFormatter->asJson($response, $data);
    }

    private function isWorkoutPlanExist(Response $response, array $data): WorkoutPlan|Response
    {
        $workoutPlan = $this->workoutPlanService->getById((int) $data['id']);

        return $workoutPlan ?? $response->withStatus(404); // checks if workout plan exists then returns it or response with 404 status
    }

    private function createTrainingDays(int $trainingsPerWeek, WorkoutPlan $workoutPlan): void
    {
        for ($i=0; $i < $trainingsPerWeek; $i++) {
            $this->trainingDayService->create($workoutPlan); // creating new training day
        }
    }

    private function validateRequestData(string $class, array $data): array
    {
        return $this->requestValidatorFactory->make($class)->validate($data);
    }
}
