<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Config;
use App\DTO\SessionOptions;

interface SessionOptionInterface
{
    public static function setOptions(Config $config): SessionOptions;
}