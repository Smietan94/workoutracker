<?php

declare(strict_types=1);

#region Use-Statements
use App\Controllers\AuthController;
use App\Controllers\CategoriesController;
use App\Controllers\ExerciseController;
use App\Controllers\HomeController;
use App\Controllers\TrainingPlanController;
use App\Controllers\WorkoutPlansController;
use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;
#endregion

return function (App $app) {
    $app->get('/', [HomeController::class, 'index'])->add(AuthMiddleware::class);

    $app->group('', function (RouteCollectorProxy $guest) {
        $guest->get('/login', [AuthController::class, 'loginView']);
        $guest->get('/register', [AuthController::class, 'registerView']);
        $guest->post('/login', [AuthController::class, 'logIn']);
        $guest->post('/register', [AuthController::class, 'register']);
    })->add(GuestMiddleware::class);

    $app->post('/logout', [AuthController::class, 'logOut'])->add(AuthMiddleware::class);

    $app->group('/categories', function (RouteCollectorProxy $categories) {
        $categories->get('', [CategoriesController::class, 'index']);
        $categories->get('/load', [CategoriesController::class, 'load']);
        $categories->post('', [CategoriesController::class, 'store']);
        $categories->delete('/{id:[0-9]+}', [CategoriesController::class, 'delete']);
        $categories->get('/{id:[0-9]+}', [CategoriesController::class, 'get']);
        $categories->post('/{id:[0-9]+}', [CategoriesController::class, 'update']);
    })->add(AuthMiddleware::class);

    $app->group('/workoutplans', function (RouteCollectorProxy $workoutPlans) {
        $workoutPlans->get('', [WorkoutPlansController::class, 'index']);
        $workoutPlans->get('/load', [WorkoutPlansController::class, 'load']);
        $workoutPlans->post('', [WorkoutPlansController::class, 'store']);
        $workoutPlans->delete('/{id:[0-9]+}', [WorkoutPlansController::class, 'delete']);
        $workoutPlans->get('/{id:[0-9]+}', [WorkoutPlansController::class, 'get']);
        $workoutPlans->post('/{id:[0-9]+}', [WorkoutPlansController::class, 'update']);
        $workoutPlans->post('/addexercise', [WorkoutPlansController::class, 'addExercise']);
        $workoutPlans->get('/getTPW', [WorkoutPlansController::class, 'getTrainingsPerWeek']);
    })->add(AuthMiddleware::class);

    $app->group('/exercises', function (RouteCollectorProxy $exercises) {
        $exercises->get('/{id:all|[0-9]+}', [ExerciseController::class, 'index']);
        $exercises->get('/load/{id:all|[0-9]+}', [ExerciseController::class, 'load']);
    })->add(AuthMiddleware::class);

    $app->group('/trainingPlan', function (RouteCollectorProxy $trainingPlan) {
        $trainingPlan->get('/{id:[0-9]+}', [TrainingPlanController::class, 'index']);
        $trainingPlan->get('/load', [TrainingPlanController::class, 'load']);
    });
};