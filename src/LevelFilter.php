<?php

declare(strict_types=1);

namespace Firehed\SimpleLogger;

use Psr\Log\LogLevel;

class LevelFilter
{
    protected const LEVELS = [
        LogLevel::EMERGENCY => \LOG_EMERG,
        LogLevel::ALERT     => \LOG_ALERT,
        LogLevel::CRITICAL  => \LOG_CRIT,
        LogLevel::ERROR     => \LOG_ERR,
        LogLevel::WARNING   => \LOG_WARNING,
        LogLevel::NOTICE    => \LOG_NOTICE,
        LogLevel::INFO      => \LOG_INFO,
        LogLevel::DEBUG     => \LOG_DEBUG,
    ];

    public static function shouldLog(string $messageLevel, ?string $minimumLevel): bool
    {
        if ($minimumLevel === null) {
            return true;
        }
        return self::LEVELS[$messageLevel] <= self::LEVELS[$minimumLevel];
    }
}
