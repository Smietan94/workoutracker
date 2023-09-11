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
        \array_push($lengthMax, ['categoryName', 50]);
        \array_push($lengthMax, ['description', 128]);
        
        foreach($fieldNames as $key) {
            // checks value in keys array if it matches pattern -> if it's set input
            if (\preg_match('/set\d+/', $key)) {
                \array_push($lengthMax, [$key, 2]); // set max lengt of set input to 2 signs -> max set is 99 reps
            }
        }

        // adding rules to validator
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