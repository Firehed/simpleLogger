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

    /**
     * Minimum log level for the logger
     *
     * @var LogLevel::* $level
     */
    private string $level = LogLevel::DEBUG;

    public FormatterInterface $formatter;

    /**
     * Set minimum log level
     *
     * @param LogLevel::* $level
     */
    public function setLevel(string $level): void
    {
        $this->level = $level;
    }

    /**
     * Get minimum log level
     */
    public function getLevel(): string
    {
        return $this->level;
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
        // Directly access the array and values here rather than run through
        // getSyslogPriority to avoid the function calls in a potential hotspot
        if (self::LEVELS[$level] <= self::LEVELS[$this->level]) {
            $this->writeLog($level, $message, $context);
        }
    }
}
