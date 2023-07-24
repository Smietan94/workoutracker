<?php

declare(strict_types=1);

namespace App\RequestValidators;

#region Use-Statements
use App\Contracts\RequestValidatorInterface;
use App\Exception\ValidationException;
use Valitron\Validator;
#endregion

class RegisterExerciseRequestValidator implements RequestValidatorInterface
{
    public function validate(array $data): array
    {
        $v = new Validator($data); 

        $fieldNames = \array_keys($data);
        $lengthMax = [];

        for ($i = 0; $i < \count($fieldNames); $i++) {
            if (! \in_array($fieldNames[$i], ['csrf_name', 'csrf_value', 'name', 'trainingDay'])) {
                \array_push($lengthMax, [$fieldNames[$i], 2]);
            }
        }

        $v->rules([
            'required' => $fieldNames,
            'lengthMax' => $lengthMax
        ]);

        if(! $v->validate()) {
            throw new ValidationException($v->errors());
        }

        return $data;
    }
}