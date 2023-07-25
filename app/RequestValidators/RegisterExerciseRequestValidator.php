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

        foreach($fieldNames as $key) {
            if (\preg_match('/set\d+/', $key)) {
                \array_push($lengthMax, [$key, 2]);
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