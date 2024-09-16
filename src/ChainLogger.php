<?php

declare(strict_types=1);

namespace Firehed\SimpleLogger;

use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Stringable;
use TypeError;

class ChainLogger extends AbstractLogger
{
    /**
     * @param LoggerInterface[] $loggers
     * @param LogLevel::*|null $level
     */
    public function __construct(private array $loggers = [], public ?string $level = null)
    {
        foreach ($loggers as $logger) {
            if (!$logger instanceof LoggerInterface) {
                throw new TypeError('Non-PSR/Log object passed to chain logger');
            }
        }
    }

    /**
     * Adds an additional logger to the chain
     */
    public function addLogger(LoggerInterface $logger): void
    {
        $this->loggers[] = $logger;
    }

    /**
     * @param LogLevel::* $level
     */
    public function log($level, string|\Stringable $message, array $context = []): void
    {
        if (!LevelFilter::shouldLog(messageLevel: $level, minimumLevel: $this->level)) {
            return;
        }

        foreach ($this->loggers as $logger) {
            $logger->log($level, $message, $context);
        }
    }
}
