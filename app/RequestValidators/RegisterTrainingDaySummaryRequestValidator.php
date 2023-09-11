<?php

declare(strict_types=1);

namespace App\RequestValidators;

#region Use-Statements
use App\Contracts\RequestValidatorInterface;
use App\Contracts\SessionInterface;
use App\Entity\TrainingDay;
use App\Exception\ValidationException;
use Doctrine\ORM\EntityManager;
use Valitron\Validator;
#endregion

class RegisterTrainingDaySummaryRequestValidator implements RequestValidatorInterface
{
    public function __construct(
        private readonly EntityManager $entityManager,
        private readonly SessionInterface $session
    ) {
    }

    public function validate(array $data): array
    {
        $v = new Validator($data);

        $requiredInputs = [
            'exercises.*.weight',
            'trainingDayDate'
        ];
        $dateInputs = [
            'trainingDayDate',
        ];
        $numericInputs  = [
            'exercises.*.weight'
        ];

        // setting max string length for inputs
        $lengthMax = [
            'exercises.*.weight' => 3,
            'exercises.*.notes'  => 140,
            'trainingDayDate'    => 10,
            'trainingDayNotes'   => 140,
        ];

        // Setting labels
        $v->labels([
            'exercises.*.weight' => 'Weight',
            'exercises.*.notes'  => 'Notes',
            'trainingDayDate'    => 'Training Day Date',
            'trainingDayNotes'   => 'Training Day Notes'
        ]);

        $v->rules([
            'required'  => $requiredInputs,
            'numeric'   => $numericInputs,
            'date'      => $dateInputs,
            'lengthMax' => $lengthMax
        ]);

        // Checking if workout plan does exist
        $v->rule(function ($field, $value, $params, $fields) {
            return $this->entityManager->find(TrainingDay::class, (int) $value);
        }, 'trainingDayId')->message('Workout plan does not exists');

        // Checking if valid user tries to register summary
        $v->rule(function ($field, $value, $params, $fields) {
            $trainingDay  = $this->entityManager->find(TrainingDay::class, (int) $value);
            if ($trainingDay) {
                $parsedUserId = $trainingDay->getWorkoutPlan()->getUser()->getId();
                return $parsedUserId === $this->session->get('user');
            }
        }, 'trainingDayId')->message('Not your workout plan');

        if (!$v->validate()) {
            throw new ValidationException($v->errors());
        }

        return $data;
    }
}