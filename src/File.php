<?php

namespace Firehed\SimpleLogger;

use RuntimeException;

/**
 * File logger
 *
 * @package SimpleLogger
 * @author  Frédéric Guillot
 */
class File extends Base
{
    /**
     * @var resource
     */
    protected $fh;

    /**
     * @var bool
     */
    private $lock = false;

    /**
     * Setup logger configuration
     *
     * @param string $filename Output file
     */
    public function __construct($filename)
    {
        $this->lock = substr($filename, 0, 6) !== 'php://';
        $this->fh = fopen($filename, 'a');
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed  $level
     * @param string $message
     * @param array  $context
     */
    protected function writeLog($level, $message, array $context = array())
    {
        $line = $this->formatMessage($level, $message, $context);

        if ($this->lock) {
            flock($this->fh, LOCK_EX);
        }
        if (fwrite($this->fh, $line) === false) {
            throw new RuntimeException('Unable to write to the log file.');
        }
        if ($this->lock) {
            flock($this->fh, LOCK_UN);
        }
    }
}
