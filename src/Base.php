<?php

declare(strict_types=1);

namespace Firehed\SimpleLogger;

use BadMethodCallException;
use DateTime;
use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;
use Throwable;

/**
 * Base class for loggers
 *
 * @package SimpleLogger
 * @author  Frédéric Guillot
 */
abstract class Base extends AbstractLogger implements ConfigurableLoggerInterface
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

    private const EXCEPTION_INTREPOLATION_KEY = 'simplelogger-internal-exception-render';

    /**
     * The date format to use in the {date} section of the log message. Stanard
     * PHP date() formatting rules apply.
     *
     * @var string
     */
    private $dateFormat = DateTime::ATOM;

    /**
     * The complete log message format, including prefixes.
     *
     * @var string
     */
    private $format = '[{date}] [{level}] %s';

    /**
     * Minimum log level for the logger
     *
     * @var    string
     */
    private $level = LogLevel::DEBUG;

    /**
     * Whether to render exceptions in context automatically
     *
     * @var bool
     */
    private $renderExceptions = false;

    /**
     * Set minimum log level
     *
     * @param  string  $level
     */
    public function setLevel(string $level): void
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
     * Set the date format. Any format string that date() accepts will work.
     *
     * @param string $format The format string
     * @return void
     */
    public function setDateFormat(string $format)
    {
        $this->dateFormat = $format;
    }

    public function setRenderExceptions(bool $render): void
    {
        $this->renderExceptions = $render;
    }

    /**
     * Set the message format. {date} and {level} will be substituted. '%s'
     * must be present somewhere in the string, and the actual interpolated
     * message being logged will be put there.
     *
     * @param string $format The format string
     */
    public function setFormat(string $format): void
    {
        if (false === strpos($format, '%s')) {
            throw new BadMethodCallException('Format string must contain %s');
        }
        $this->format = $format;
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

    /**
     * @param LogLevel::* $level
     * @param string $message
     * @param array<string, string> $context
     * @return void
     */
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
     * @deprecated in v2.2.0, will be removed in v3.0.0
     * @param mixed $variable
     * @return void
     */
    public function dump($variable)
    {
        trigger_error(sprintf('%s is deprecated', __METHOD__), E_USER_DEPRECATED);
        $this->log(LogLevel::DEBUG, var_export($variable, true));
    }

    /**
     * Interpolates context values into the message placeholders.
     *
     * @access protected
     * @param  string $message
     * @param  array<string, string> $context
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
     * @param  array<string, mixed> $context
     * @return string
     */
    protected function formatMessage($level, $message, array $context = array())
    {
        $formatData = [
            'level' => $level,
            'date' => date($this->dateFormat),
        ];

        if ($this->renderExceptions && array_key_exists('exception', $context)) {
            if ($context['exception'] instanceof Throwable) {
                $exceptionMessage = (string) $context['exception'];
                $message .= ' {' . self::EXCEPTION_INTREPOLATION_KEY . '}';
                $context[self::EXCEPTION_INTREPOLATION_KEY] = $exceptionMessage;
            } else {
                trigger_error('context.exception is not a Throwable', E_USER_ERROR);
            }
        }

        return sprintf(
            $this->interpolate($this->format, $formatData),
            $this->interpolate($message, $context)
        ) . PHP_EOL;
    }
}
