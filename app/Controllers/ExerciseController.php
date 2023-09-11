<?php

declare(strict_types=1);

namespace App\Controllers;

#region Use-Statements
use App\Contracts\RequestValidatorFactoryInterface;
use App\Contracts\SessionInterface;
use App\Entity\Exercise;
use App\ResponseFormatter;
use App\Services\ExerciseService;
use App\Services\RequestService;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;
#endregion

class ExerciseController
{
    public function __construct(
        private readonly Twig $twig,
        private readonly RequestValidatorFactoryInterface $requestValidatorFactory,
        private readonly ExerciseService $exerciseService,
        private readonly ResponseFormatter $responseFormatter,
        private readonly RequestService $requestService,
        private readonly SessionInterface $session
    ) {
    }

    public function index(Request $request, Response $response, array $args): Response
    {
        return $this->twig->render(
            $response,
            'exercises/index.twig'
        );
    }

    public function load(Request $request, Response $response, array $args): Response
    {
        $params = $this->requestService->getDataTableQueryParams($request);

        // Id is validated in web.php
        if ($args['id'] === 'all' ) {
            $exercises = $this->exerciseService->getAllPaginatedExercises($params);
        } else {
            $categoryId = (int) $args['id'];
            $exercises = $this->exerciseService->getPaginatedExercises($params, $categoryId);
        }

        $transformer = function (Exercise $exercise) {
            return [
                'id' => $exercise->getId(),
                'exerciseName' => $exercise->getExerciseName(),
                'description' => $exercise->getDescription(),
            ];
        };

        $total = \count($exercises);

        return $this->responseFormatter->asDataTable(
            $response,
            \array_map($transformer, (array) $exercises->getIterator()),
            $params->draw,
            $total,
            $total
        );
    }
}