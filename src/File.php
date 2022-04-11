<?php

declare(strict_types=1);

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
        $fh = fopen($filename, 'a');
        if ($fh === false) {
            throw new RuntimeException('Could not open log file');
        }
        $this->fh = $fh;
    }

    protected function writeLog($level, $message, array $context = array()): void
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
