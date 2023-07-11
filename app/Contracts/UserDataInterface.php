<?php

declare(strict_types=1);

namespace App\Contracts;

use App\DTO\UserData;

interface UserDataInterface
{
    public static function setUserData(array $data): UserData;
}