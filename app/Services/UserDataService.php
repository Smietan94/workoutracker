<?php

declare(strict_types=1);

namespace App\Services;

#region Use-Statements
use App\Contracts\UserDataInterface;
use App\DTO\UserData;
#endregion

class UserDataService implements UserDataInterface
{
    public static function setUserData(array $data): UserData
    {
        return new UserData(
            $data['name'],
            $data['username'],
            $data['email'],
            password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]),
        );
    }
}