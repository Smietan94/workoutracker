<?php

declare(strict_types=1);

namespace App\RequestValidators;

#region Use-Statements
use App\Contracts\RequestValidatorInterface;
use App\Exception\ValidationException;
use Valitron\Validator;
#endregion

class UpdateWorkoutPlanRequestValidator implements RequestValidatorInterface
{
    public function validate(array $data): array
    {
        $v = new Validator($data);

        $v->rule('lengthMax', 'name', 50);
        $v->rule('lengthMax', 'notes', 100);

        if (! $v->validate()) {
            throw new ValidationException($v->errors());
        }

        return $data;
    }
}