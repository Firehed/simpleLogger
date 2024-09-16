<?php

declare(strict_types=1);

namespace Firehed\SimpleLogger;

use RuntimeException;
use Stringable;

class File extends Base
{
    /**
     * @var resource
     */
    protected $fh;

    private bool $lock = false;

    /**
     * Setup logger configuration
     *
     * @param string $filename Output file
     */
    public function __construct(string $filename, FormatterInterface $formatter = new DefaultFormatter())
    {
        $this->lock = substr($filename, 0, 6) !== 'php://';
        $fh = fopen($filename, 'a');
        if ($fh === false) {
            throw new RuntimeException('Could not open log file');
        }
        $this->fh = $fh;
        $this->formatter = $formatter;
    }

    protected function write(string $level, string $message): void
    {
        if ($this->lock) {
            flock($this->fh, LOCK_EX);
        }
        if (fwrite($this->fh, $message . \PHP_EOL) === false) {
            throw new RuntimeException('Unable to write to the log file.');
        }
        if ($this->lock) {
            flock($this->fh, LOCK_UN);
        }
    }
}
