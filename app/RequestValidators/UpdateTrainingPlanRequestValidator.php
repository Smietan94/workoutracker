<?php

declare(strict_types=1);

namespace App\RequestValidators;

#region Use-Statements
use App\Contracts\RequestValidatorInterface;
use App\Contracts\SessionInterface;
use App\Entity\WorkoutPlan;
use App\Exception\ValidationException;
use App\Session;
use Doctrine\ORM\EntityManager;
use Valitron\Validator;
#endregion

class UpdateTrainingPlanRequestValidator implements RequestValidatorInterface
{
    public function __construct(
        private readonly EntityManager $entityManager,
        private readonly SessionInterface $session,
    ) {
    }

    public function validate(array $data): array
    {
        $v = new Validator($data);

        $requiredInputs = [
            'trainingDays.*.exercises.*.exerciseName',
            'trainingDays.*.exercises.*.description',
            'trainingDays.*.exercises.*.category',
            'trainingDays.*.exercises.*.sets.*'
        ];
        $numericInputs = [
            'trainingDays.*.exercises.*.sets.*',
        ];
        $lengthMax = [
            ['trainingDays.*.exercises.*.exerciseName', 50],
            ['trainingDays.*.exercises.*.description', 250],
            ['trainingDays.*.exercises.*.category', 50],
            ['trainingDays.*.exercises.*.sets.*', 3]
        ];

        $v->labels([
            'trainingDays.*.exercises.*.exerciseName' => 'Exercise Name',
            'trainingDays.*.exercises.*.description'  => 'Description',
            'trainingDays.*.exercises.*.category'     => 'Category',
            'trainingDays.*.exercises.*.sets.*'       => 'Set',
            'workoutPlanId'                           => 'User'
        ]);

        $v->rules([
            'required'  => $requiredInputs,
            'numeric'   => $numericInputs,
            'lengthMax' => $lengthMax
        ]);

        $v->rule(function ($field, $value, $params, $fields) {
            $workoutPlan = $this->entityManager->find(WorkoutPlan::class, (int) $value);
            $userId = $workoutPlan->getUser()->getId();
            return $userId === $this->session->get('user');
        }, 'workoutPlanId')->message('Not your workout plan');

        if (!$v->validate()) {
            throw new ValidationException($v->errors());
        }

        return $data;
    }
}
