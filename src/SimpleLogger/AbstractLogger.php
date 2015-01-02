<?php

namespace SimpleLogger;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * Abstract logger class
 *
 * @package SimpleLogger
 * @author  Frédéric Guillot
 */
abstract class AbstractLogger implements LoggerInterface
{
    /**
     * Dump to log a variable (by example an array)
     *
     * @param mixed $variable
    */
    public function dump($variable)
    {
        $this->log(LogLevel::DEBUG, var_export($variable, true));
    }

    /**
     * Interpolates context values into the message placeholders.
     *
     * @access protected
     * @param  string $message
     * @param  array $context
     * @return string
    */
    protected function interpolate($message, array $context = array())
    {
        // build a replacement array with braces around the context keys
        $replace = array();

        foreach ($context as $key => $val) {
          $replace['{' . $key . '}'] = $val;
        }

        // interpolate replacement values into the message and return
        return strtr($message, $replace);
    }

    /**
     * System is unusable.
     *
     * @access public
     * @param  string  $message
     * @param  array   $context
     */
    public function emergency($message, array $context = array())
    {
        $this->log(LogLevel::EMERGENCY, $message, $context);
    }

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @access public
     * @param  string  $message
     * @param  array   $context
     */
    public function alert($message, array $context = array())
    {
        $this->log(LogLevel::ALERT, $message, $context);
    }

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @access public
     * @param  string  $message
     * @param  array   $context
     */
    public function critical($message, array $context = array())
    {
        $this->log(LogLevel::CRITICAL, $message, $context);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param  string  $message
     * @param  array   $context
     */
    public function error($message, array $context = array())
    {
        $this->log(LogLevel::ERROR, $message, $context);
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @access public
     * @param  string  $message
     * @param  array   $context
     */
    public function warning($message, array $context = array())
    {
        $this->log(LogLevel::WARNING, $message, $context);
    }

    /**
     * Normal but significant events.
     *
     * @access public
     * @param  string  $message
     * @param  array   $context
     * @return null
     */
    public function notice($message, array $context = array())
    {
        $this->log(LogLevel::NOTICE, $message, $context);
    }

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @access public
     * @param  string  $message
     * @param  array   $context
     * @return null
     */
    public function info($message, array $context = array())
    {
        $this->log(LogLevel::INFO, $message, $context);
    }

    /**
     * Detailed debug information.
     *
     * @access public
     * @param  string $message
     * @param  array  $context
     */
    public function debug($message, array $context = array())
    {
        $this->log(LogLevel::DEBUG, $message, $context);
    }
}
