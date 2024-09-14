<?php

declare(strict_types=1);

namespace Firehed\SimpleLogger;

use Psr\Log\LogLevel;
use Stringable;

class Syslog extends Base
{
    private const LEVELS = [
        LogLevel::EMERGENCY => \LOG_EMERG,
        LogLevel::ALERT     => \LOG_ALERT,
        LogLevel::CRITICAL  => \LOG_CRIT,
        LogLevel::ERROR     => \LOG_ERR,
        LogLevel::WARNING   => \LOG_WARNING,
        LogLevel::NOTICE    => \LOG_NOTICE,
        LogLevel::INFO      => \LOG_INFO,
        LogLevel::DEBUG     => \LOG_DEBUG,
    ];

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
