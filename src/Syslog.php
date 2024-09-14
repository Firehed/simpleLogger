<?php

declare(strict_types=1);

namespace Firehed\SimpleLogger;

use Psr\Log\LogLevel;
use Stringable;

class Syslog extends Base
{
    /**
     * Setup Syslog configuration
     *
     * @param $ident Application name
     * @param  int    $facility See http://php.net/manual/en/function.openlog.php
     */
    public function __construct(string $ident = 'PHP', int $facility = LOG_USER)
    {
        openlog($ident, LOG_ODELAY | LOG_PID, $facility);
    }

    protected function writeLog($level, string|Stringable $message, array $context = []): void
    {
        $syslogPriority = $this->getSyslogPriority($level);
        $syslogMessage = $this->interpolate($message, $context);

        syslog($syslogPriority, $syslogMessage);
    }

    protected function getSyslogPriority(string $psrLevel): int
    {
        return self::LEVELS[$psrLevel];
    }
}
