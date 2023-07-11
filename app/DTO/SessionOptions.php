<?php

declare(strict_types=1);

namespace App\DTO;

use App\Enum\SameSite;

class SessionOptions
{
    public function __construct(
        public readonly string $name,
        public readonly string $flashName,
        public readonly bool $secure,
        public readonly bool $httponly,
        public readonly SameSite $samesite,
    ){
    }
}
