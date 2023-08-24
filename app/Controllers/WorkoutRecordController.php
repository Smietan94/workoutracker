<?php

declare(strict_types=1);

namespace App\Controllers;

#region Use-Statements

use App\Contracts\RequestValidatorFactoryInterface;
use App\ResponseFormatter;
use App\Services\RequestService;
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
    ) {
    }

    public function index(Request $request, Response $response): Response
    {
        return $this->twig->render(
            $response,
            'workoutRecord/index.twig'
        );
    }
}