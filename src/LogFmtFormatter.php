<?php

declare(strict_types=1);

namespace Firehed\SimpleLogger;

use DateTimeImmutable;
use Psr\Clock\ClockInterface;
use Stringable;

/**
 * Uses the `logfmt` format when writing logs.
 *
 * Any entries in `context` that are not interpolated into the message are
 * added as additional key/value pairs in the output. Given the nature of
 * `logfmt`, it's generally preferable to minimize interpolated values in favor
 * of strucured k/v pairs, but that's your call.
 *
 * Standard output keys for the message, level, and timestamp are configurable
 * through the constructor. Setting keys to null will result in the pair being
 * omitted. Similarly, nulling timestampFormat will result in the timestamp
 * being omitted, even if the timestampKey is set.
 *
 * You may optionally pass a `Psr\Clock\ClockInterface`. If one is provied, it
 * will be used when creating the timestamp; otherwise `new DateTimeImmutable`
 * will be how a timestamp is determined.
 *
 * @see https://brandur.org/logfmt
 */
class LogFmtFormatter implements FormatterInterface
{
    public function __construct(
        private readonly string $messageKey = 'msg',
        private readonly ?string $levelKey = 'level',
        private readonly ?string $timestampKey = 'ts',
        private readonly ?string $timestampFormat = DateTimeImmutable::RFC3339,
        private readonly ?ClockInterface $clock = null,
    ) {
    }

    public function format(string $level, string|Stringable $message, array $context = []): string
    {
        // Stringable to string
        $message = (string)$message;

        // Interpolate message normally
        $pairs = [];
        foreach ($context as $key => $value) {
            $from = '{' . $key . '}';
            $willTranslate = str_contains(needle: $from, haystack: $message);
            if ($willTranslate) {
                $pairs[$from] = $value;
                unset($context[$key]);
            }
        }
        $formattedMessage = strtr($message, $pairs);

        // Prepare second-pass encoding
        $pairs = $context;
        $pairs[$this->messageKey] = $formattedMessage;
        if ($this->levelKey !== null) {
            $pairs[$this->levelKey] = $level;
        }
        if ($this->timestampKey !== null && $this->timestampFormat !== null) {
            $ts = $this->clock?->now() ?? new DateTimeImmutable();
            $pairs[$this->timestampKey] = $ts->format($this->timestampFormat);
        }

        // AFTER interpolating, read out exception if present and munge it into
        // new context.
        if (array_key_exists('exception', $pairs)) {
            $ex = $pairs['exception'];
            assert($ex instanceof \Throwable);
            unset($pairs['exception']);
            $pairs['exception_message'] = $ex->getMessage();
            $pairs['exception_type'] = get_class($ex);
            $pairs['exception_stacktrace'] = $ex->getTraceAsString();
        }

        $formattedPairs = [];
        foreach ($pairs as $key => $value) {
            if (!is_string($value)) {
                $value = var_export($value, true);
            }
            $hasSpaces = str_contains(needle: ' ', haystack: $value);
            $hasQuotes = str_contains(needle: '"', haystack: $value);
            if ($hasSpaces || $hasQuotes) {
                $escapedValue = json_encode($value, JSON_UNESCAPED_SLASHES);
            } else {
                $escapedValue = $value;
            }
            $formattedPairs[] = sprintf('%s=%s', $key, $escapedValue);
        }

        return implode(' ', $formattedPairs);
    }
}
