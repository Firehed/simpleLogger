<?php

namespace SimpleLogger;

use DateTime;
use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;

/**
 * Base class for loggers
 *
 * @package SimpleLogger
 * @author  Frédéric Guillot
 */
abstract class Base extends AbstractLogger
{
    private const LEVELS = [
        LogLevel::EMERGENCY => LOG_EMERG,
        LogLevel::ALERT     => LOG_ALERT,
        LogLevel::CRITICAL  => LOG_CRIT,
        LogLevel::ERROR     => LOG_ERR,
        LogLevel::WARNING   => LOG_WARNING,
        LogLevel::NOTICE    => LOG_NOTICE,
        LogLevel::INFO      => LOG_INFO,
        LogLevel::DEBUG     => LOG_DEBUG,
    ];

    /**
     * Minimum log level for the logger
     *
     * @access private
     * @var    string
     */
    private $level = LogLevel::DEBUG;

    /**
     * Set minimum log level
     *
     * @access public
     * @param  string  $level
     */
    public function setLevel($level)
    {
        $this->level = $level;
    }

    /**
     * Get minimum log level
     *
     * @access public
     * @return string
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Get the syslog priority constant associated with the current level
     */
    public function getCurrentSyslogPriority(): int
    {
        return $this->getSyslogPriority($this->getLevel());
    }

    /**
     * @param string $psrLevel PSR log level
     * @return int LOG_ constant
     */
    protected function getSyslogPriority($psrLevel)
    {
        return self::LEVELS[$psrLevel];
    }

    abstract protected function writeLog($level, $message, array $context = []);

    public function log($level, $message, array $context = array())
    {
        // Directly access the array and values here rather than run through
        // getSyslogPriority to avoid the function calls in a potential hotspot
        if (self::LEVELS[$level] <= self::LEVELS[$this->level]) {
            $this->writeLog($level, $message, $context);
        }
    }

    /**
     * Dump to log a variable (by example an array)
     *
     * @param mixed $variable
     */
    public function dump($variable)
    {
        $this->log(LogLevel::DEBUG, var_export($variable, true));
    }

    /**
     * Interpolates context values into the message placeholders.
     *
     * @access protected
     * @param  string $message
     * @param  array $context
     * @return string
     */
    protected function interpolate($message, array $context = array())
    {
        // build a replacement array with braces around the context keys
        $replace = array();

        foreach ($context as $key => $val) {
            $replace['{' . $key . '}'] = $val;
        }

        // interpolate replacement values into the message and return
        return strtr($message, $replace);
    }

    /**
     * Format log message
     *
     * @param  mixed  $level
     * @param  string $message
     * @param  array  $context
     * @return string
     */
    protected function formatMessage($level, $message, array $context = array())
    {
        return '['.date(DateTime::ATOM).'] ['.$level.'] '.$this->interpolate($message, $context).PHP_EOL;
    }
}
