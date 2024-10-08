<?php

declare(strict_types=1);

namespace Firehed\SimpleLogger;

use BadMethodCallException;
use DateTime;
use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;
use Stringable;
use Throwable;

abstract class Base extends AbstractLogger implements ConfigurableLoggerInterface
{
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
     *
     * @return LogLevel::*
     */
    public function getLevel(): string
    {
        return $this->level;
    }

    /**
     * @param LogLevel::* $level
     */
    abstract protected function write(string $level, string $message): void;

    /**
     * @param LogLevel::* $level
     * @param array<string, mixed> $context
     */
    public function log($level, string|Stringable $message, array $context = array()): void
    {
        if (LevelFilter::shouldLog(messageLevel: $level, minimumLevel: $this->level)) {
            $formatted = $this->formatter->format($level, $message, $context);
            $this->write($level, $formatted);
        }
    }
}
