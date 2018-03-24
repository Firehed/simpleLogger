<?php

namespace SimpleLogger;

/**
 * Stdout logger
 *
 * @package SimpleLogger
 * @author  Frédéric Guillot
 */
class Stdout extends Base
{
    /**
     * Logs with an arbitrary level.
     *
     * @param mixed  $level
     * @param string $message
     * @param array  $context
     * @return null
     */
    protected function writeLog($level, $message, array $context = array())
    {
        file_put_contents('php://stdout', $this->formatMessage($level, $message, $context), FILE_APPEND);
    }
}
