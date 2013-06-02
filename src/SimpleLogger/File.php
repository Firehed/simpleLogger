<?php

namespace SimpleLogger;

require_once __DIR__.'/AbstractLogger.php';

use \Psr\Log\LogLevel;

/**
 * Simple text file output implementation
 */
class File extends AbstractLogger
{
    private $filename = '';

    /**
     * Setup Syslog configuration
     *
     * @param string $filename Output file
     * @return null
     */
    public function __construct($filename)
    {
        $this->filename = $filename;
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return null
     */
    public function log($level, $message, array $context = array())
    {
        $line = '['.\date('Y-m-d H:i:s').'] ['.$level.'] '.$this->interpolate($message, $context)."\n";
        \file_put_contents($this->filename, $line, FILE_APPEND | LOCK_EX);
    }
}
