<?php

namespace SimpleLogger;

use Psr\Log\AbstractLogger;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

/**
 * Handler for multiple loggers
 *
 * @package SimpleLogger
 * @author  Frédéric Guillot
 */
class Logger extends AbstractLogger implements LoggerAwareInterface
{
    /**
     * Logger instances
     *
     * @access private
     */
    private $loggers = array();

    /**
     * Sets a logger instance on the object
     *
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->loggers[] = $logger;
    }

    /**
     * Proxy method to the real logger
     *
     * @param  mixed   $level
     * @param  string  $message
     * @param  array   $context
     */
    public function log($level, $message, array $context = array())
    {
        foreach ($this->loggers as $logger) {
            $logger->log($level, $message, $context);
        }
    }
}
