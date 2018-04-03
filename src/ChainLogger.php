<?php

namespace Firehed\SimpleLogger;

use Psr\Log\AbstractLogger;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * Handler for multiple loggers
 *
 * @package SimpleLogger
 * @author  Frédéric Guillot
 */
class ChainLogger extends Base
{
    /**
     * Logger instances
     */
    private $loggers = [];

    public function __construct(array $loggers = [])
    {
        foreach ($loggers as $logger) {
            $this->addLogger($logger);
        }
    }

    /**
     * Sets a logger instance on the object
     *
     * @param  LoggerInterface $logger
     * @return null
     */
    public function addLogger(LoggerInterface $logger)
    {
        $this->loggers[] = $logger;
    }

    /**
     * Proxy method to the real loggers
     *
     * @param  mixed   $level
     * @param  string  $message
     * @param  array   $context
     * @return null
     */
    protected function writeLog($level, $message, array $context = array())
    {
        foreach ($this->loggers as $logger) {
            $logger->log($level, $message, $context);
        }
    }
}
