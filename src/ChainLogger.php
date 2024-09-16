<?php

declare(strict_types=1);

namespace Firehed\SimpleLogger;

use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Stringable;

class ChainLogger extends AbstractLogger
{
    /**
     * @param LoggerInterface[] $loggers
     * @param LogLevel::*|null $logLevel
     */
    public function __construct(private array $loggers = [], public ?string $logLevel = null)
    {
    }

    /**
     * Adds an additional logger to the chain
     */
    public function addLogger(LoggerInterface $logger): void
    {
        $this->loggers[] = $logger;
    }

    public function log($level, string|\Stringable $message, array $context = []): void
    {

        foreach ($this->loggers as $logger) {
            $logger->log($level, $message, $context);
        }
    }
}
