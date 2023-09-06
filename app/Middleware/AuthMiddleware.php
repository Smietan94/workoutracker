<?php

declare(strict_types=1);

namespace App\Middleware;

#region Use-Statements
use App\Contracts\AuthInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Views\Twig;

#endregion

class AuthMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly ResponseFactoryInterface $responseFactoryInterface,
        private readonly AuthInterface $auth,
        private readonly Twig $twig
    ){
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($user = $this->auth->user()) {
            $this->twig->getEnvironment()->addGlobal('auth', [
                'id'                => $user->getId(), 
                'username'          => $user->getUsername(), 
                'mainWorkoutPlanId' => $user->getMainWorkoutPlanId(),
            ]);

            return $handler->handle($request->withAttribute('user', $user));
        }

        return $this->responseFactoryInterface->createResponse(302)->withHeader('Location', '/login');
    }
}
