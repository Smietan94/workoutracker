<?php

declare(strict_types=1);

namespace App\RequestValidators;

#region Use-Statements
use App\Contracts\RequestValidatorInterface;
use App\Exception\ValidationException;
use Valitron\Validator;
#endregion

class UpdateCategoryRequestValidator implements RequestValidatorInterface
{
    public function validate(array $data): array
    {
        $v = new Validator($data);

        $v->rule('required', ['name', 'id']);
        $v->rule('lengthMax', 'name', 50);
        $v->rule('integer', 'id');

        if (!$v->validate()) {
            throw new ValidationException($v->errors());
        }

        return $data;
    }
}
