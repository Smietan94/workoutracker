<?php

declare(strict_types=1);

namespace App\Middleware;

#region Use-Statements
use App\Contracts\SessionInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Views\Twig;
#endregion

class OldFormDataMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly Twig $twig,
        private readonly SessionInterface $session,
    ){
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($old = $this->session->getFlash('old')) {
            $this->twig->getEnvironment()->addGlobal('old', $old);
        }

        return $handler->handle($request);
    }
}
