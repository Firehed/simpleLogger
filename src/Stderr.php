<?php

declare(strict_types=1);

namespace Firehed\SimpleLogger;

/**
 * Stderr logger
 *
 * @package SimpleLogger
 * @author  Frédéric Guillot
 */
class Stderr extends File
{
    public function __construct(FormatterInterface $formatter = new DefaultFormatter())
    {
        parent::__construct('php://stderr', $formatter);
    }
}
