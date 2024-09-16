<?php

declare(strict_types=1);

namespace Firehed\SimpleLogger;

use BadMethodCallException;
use DateTimeInterface;
use Stringable;
use Throwable;

class DefaultFormatter implements FormatterInterface
{
    private const EXCEPTION_INTREPOLATION_KEY = 'simplelogger-internal-exception-render';

    /**
     * The date format to use in the {date} section of the log message. Stanard
     * PHP date() formatting rules apply.
     */
    private string $dateFormat = DateTimeInterface::ATOM;

    /**
     * The complete log message format, including prefixes.
     *
     * @var string
     */
    private $format = '[{date}] [{level}] %s';

    /**
     * Whether to render exceptions in context automatically
     *
     * @var bool
     */
    private $renderExceptions = false;

    public function format(string $level, string|Stringable $message, array $context = []): string
    {
        $formatData = [
            'level' => $level,
            'date' => date($this->dateFormat),
        ];

        if ($this->renderExceptions && array_key_exists('exception', $context)) {
            if ($context['exception'] instanceof Throwable) {
                $exceptionMessage = (string) $context['exception'];
                $message .= ' {' . self::EXCEPTION_INTREPOLATION_KEY . '}';
                $context[self::EXCEPTION_INTREPOLATION_KEY] = $exceptionMessage;
            }
        }

        return sprintf(
            $this->interpolate($this->format, $formatData),
            $this->interpolate($message, $context)
        ) . PHP_EOL;
    }

    /**
     * Set the date format. Any format string that date() accepts will work.
     *
     * @param string $format The format string
     * @return void
     */
    public function setDateFormat(string $format)
    {
        $this->dateFormat = $format;
    }

    /**
     * Set the message format. {date} and {level} will be substituted. '%s'
     * must be present somewhere in the string, and the actual interpolated
     * message being logged will be put there.
     *
     * @param string $format The format string
     */
    public function setFormat(string $format): void
    {
        if (false === strpos($format, '%s')) {
            throw new BadMethodCallException('Format string must contain %s');
        }
        $this->format = $format;
    }

    public function setRenderExceptions(bool $render): void
    {
        $this->renderExceptions = $render;
    }

    /**
     * Interpolates context values into the message placeholders.
     *
     * @param string|\Stringable $message
     * @param array<string, mixed> $context
     */
    protected function interpolate($message, array $context = array()): string
    {
        // build a replacement array with braces around the context keys
        $replace = array();

        foreach ($context as $key => $val) {
            $replace['{' . $key . '}'] = $val;
        }

        // interpolate replacement values into the message and return
        return strtr((string)$message, $replace);
    }
}
