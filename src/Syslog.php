<?php

namespace Firehed\SimpleLogger;

use RuntimeException;
use Psr\Log\LogLevel;

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
        if (! openlog($ident, LOG_ODELAY | LOG_PID, $facility)) {
            throw new RuntimeException('Unable to connect to syslog.');
        }
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param  mixed   $level
     * @param  string  $message
     * @param  array   $context
     * @return null
     */
    protected function writeLog($level, $message, array $context = array())
    {
        $syslogPriority = $this->getSyslogPriority($level);
        $syslogMessage = $this->interpolate($message, $context);

        syslog($syslogPriority, $syslogMessage);
    }
}
