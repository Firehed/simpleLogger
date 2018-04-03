<?php

namespace Firehed\SimpleLogger;

/**
 * Stdout logger
 *
 * @package SimpleLogger
 * @author  Frédéric Guillot
 */
class Stdout extends File
{
    public function __construct()
    {
        parent::__construct('php://stdout');
    }
}
