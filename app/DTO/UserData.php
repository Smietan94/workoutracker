<?php

declare(strict_types=1);

namespace App\DTO;

class UserData
{
    public function __construct(
        public readonly string $name,
        public readonly string $username,
        public readonly string $email,
        public readonly string $password,
    ) {
    }
}
