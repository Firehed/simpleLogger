<?php

declare(strict_types=1);

namespace Firehed\SimpleLogger;

use BadMethodCallException;
use DateTime;
use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;
use Stringable;
use Throwable;

/**
 * Base class for loggers
 *
 * @package SimpleLogger
 * @author  Frédéric Guillot
 */
abstract class Base extends AbstractLogger implements ConfigurableLoggerInterface
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
     * @param LogLevel::* $level
     * @param array<string, mixed> $context
     */
    abstract protected function writeLog($level, string|Stringable $message, array $context = []): void;

    /**
     * @param LogLevel::* $level
     * @param array<string, mixed> $context
     */
    public function log($level, string|Stringable $message, array $context = array()): void
    {
        if (LevelFilter::shouldLog(messageLevel: $level, minimumLevel: $this->level)) {
            $this->writeLog($level, $message, $context);
        }
    }

    /**
     * Interpolates context values into the message placeholders.
     *
     * @param string|\Stringable $message
     * @param array<string, mixed> $context
     */
    protected function interpolate($message, array $context = array()): string
    {
        // build a replacement array with braces around the context keys
        $replace = array();

        foreach ($context as $key => $val) {
            $replace['{' . $key . '}'] = $val;
        }

        // interpolate replacement values into the message and return
        return strtr((string)$message, $replace);
    }

    /**
     * Format log message
     *
     * @param LogLevel::* $level
     * @param string|\Stringable $message
     * @param array<string, mixed> $context
     */
    protected function formatMessage(string $level, $message, array $context = array()): string
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
            }
        }

        return sprintf(
            $this->interpolate($this->format, $formatData),
            $this->interpolate($message, $context)
        ) . PHP_EOL;
    }
}
