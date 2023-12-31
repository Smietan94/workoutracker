<?php

declare(strict_types=1);

namespace App\Controllers;

#region Use-Statements
use App\Contracts\AuthInterface;
use App\Contracts\RequestValidatorFactoryInterface;
use App\Exception\ValidationException;
use App\RequestValidators\RegisterUserRequestValidator;
use App\RequestValidators\UserLoginRequestValidator;
use App\Services\UserDataService;
use App\Services\UserProviderService;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;
#endregion

class AuthController
{
    public function __construct(
        private readonly Twig $twig, 
        private readonly RequestValidatorFactoryInterface $requestValidatorFactory,
        private readonly AuthInterface $auth,
        private readonly UserProviderService $userProviderService,
    ){
    }

    public function loginView(Request $request, Response $response): Response
    {
        return $this->twig->render($response, 'auth/login.twig');
    }

    public function registerView(Request $request, Response $response): Response
    {
        return $this->twig->render($response, 'auth/register.twig');
    }

    public function register(Request $request, Response $response): Response
    {
        $data = $this->requestValidatorFactory->make(RegisterUserRequestValidator::class)->validate($request->getParsedBody());

        $this->auth->register(UserDataService::setUserData($data));

        return $response->withHeader('Location', '/')->withStatus(302);
    }

    public function logIn(Request $request, Response $response): Response
    {
        $data = $this->requestValidatorFactory->make(UserLoginRequestValidator::class)->validate($request->getParsedBody());
        if (! $this->auth->attemptLogin($data)) {
            throw new ValidationException(['password' => ['You have entered invalid email or password']]);
        };
        $user = $this->userProviderService->getByCredentials(['email' => $data['email']]);
        return $response->withHeader('Location', '/' . $user->getMainWorkoutPlanId())->withStatus(302);
    }

    public function logOut(Request $erquest, Response $response): Response
    {
        $this->auth->logOut();

        return $response->withHeader('Location', '/')->withStatus(302);
    }
}
