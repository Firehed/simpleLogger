<?php

declare(strict_types=1);

namespace Firehed\SimpleLogger;

use Psr\Log\LogLevel;
use Stringable;

/**
 * Syslog Logger
 *
 * @package SimpleLogger
 * @author  Frédéric Guillot
 */
class Syslog extends Base
{
    /**
     * Setup Syslog configuration
     *
     * @param  string $ident    Application name
     * @param  int    $facility See http://php.net/manual/en/function.openlog.php
     */
    public function __construct($ident = 'PHP', $facility = LOG_USER)
    {
        openlog($ident, LOG_ODELAY | LOG_PID, $facility);
    }

    protected function writeLog($level, string|Stringable $message, array $context = array()): void
    {
        $syslogPriority = $this->getSyslogPriority($level);
        $syslogMessage = $this->interpolate($message, $context);

        syslog($syslogPriority, $syslogMessage);
    }
}
