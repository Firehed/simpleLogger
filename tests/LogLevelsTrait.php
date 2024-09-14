<?php

declare(strict_types=1);

namespace Firehed\SimpleLogger;

use Psr\Log\LogLevel;

trait LogLevelsTrait
{
    /**
     * @return array<LogLevel::*>[] The LogLevel constants, formatted for use
     * as a DataProvider
     */
    public static function allLevels(): array
    {
        return [
            [LogLevel::EMERGENCY],
            [LogLevel::ALERT],
            [LogLevel::CRITICAL],
            [LogLevel::ERROR],
            [LogLevel::WARNING],
            [LogLevel::NOTICE],
            [LogLevel::INFO],
            [LogLevel::DEBUG],
        ];
    }
}
