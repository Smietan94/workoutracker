<?php

declare(strict_types=1);

namespace App;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Response;

class Csrf
{
    public function __construct(private readonly ResponseFactoryInterface $responseFactory)
    {
    }

    public function failureHandler(): \Closure
    {
        return fn (
            Request $request,
            Response $response
        ) => $this->responseFactory->createResponse()->withStatus(403);
    }
}
