<?php 

declare(strict_types=1);

namespace App\Services;

#region Use-Statements
use App\Config;
use App\Contracts\SessionOptionInterface;
use App\DTO\SessionOptions;
use App\Enum\SameSite;
#endregion

class SessionOptionService implements SessionOptionInterface
{
    public static function setOptions(Config $config): SessionOptions
    {
        return new SessionOptions(
            $config->get('session.name', ''),
            $config->get('session.flash_name', 'flash'),
            $config->get('session.secure', true),
            $config->get('session.httponly', true),
            SameSite::from($config->get('session.samesite', 'lax'))
        );
    }
}