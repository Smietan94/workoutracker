<?php

declare(strict_types=1);

namespace App\Controllers;

#region Use-Statements
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;
#endregion

class HomeController
{
    public function __construct(private readonly Twig $twig)
    {
    }

    public function index(Request $request, Response $response): Response
    {
        return $this->twig->render($response, 'dashboard.twig');
    }
}