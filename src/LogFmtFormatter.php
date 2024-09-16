<?php

declare(strict_types=1);

namespace Firehed\SimpleLogger;

use DateTimeImmutable;
use Psr\Clock\ClockInterface;
use Stringable;

use function array_key_exists;
use function array_merge;
use function assert;
use function implode;
use function is_scalar;
use function is_string;
use function json_encode;
use function str_contains;
use function var_export;

use const JSON_THROW_ON_ERROR;
use const JSON_UNESCAPED_SLASHES;

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
 * > [!IMPORTANT]
 * > The constructor argument _order_ is not covered by backwards compatibility
 * > rules. ALWAYS use named arguments when configuring this formatter through
 * > the constructor.
 *
 * @see https://brandur.org/logfmt
 */
class LogFmtFormatter implements FormatterInterface
{
    // LogFmt doesn't really define what needs escaping, so this aims to
    // provide a sensible start.
    //
    // The order is intended to keep more likely characters first to
    // early-abort the detection loop.
    private const ESCAPED_CHARACTERS = [' ', '"', "\n", "\r", "\t"];

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

        // Interpolate message normally, removing interpolated values from the
        // context to be used as k/v pairs
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
        $pairs = [$this->messageKey => $formattedMessage];
        if ($this->levelKey !== null) {
            $pairs[$this->levelKey] = $level;
        }
        if ($this->timestampKey !== null && $this->timestampFormat !== null) {
            $ts = $this->clock?->now() ?? new DateTimeImmutable();
            $pairs[$this->timestampKey] = $ts->format($this->timestampFormat);
        }
        // Note: context is merged in _after_ pairs are built to encourage the
        // typically most important pairs to be at the front of the message.
        $pairs = array_merge($pairs, $context);

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
            $escaped = $this->escape($value);
            if ($escaped !== null) {
                $formattedPairs[] = sprintf('%s=%s', $key, $escaped);
            }
        }

        return implode(' ', $formattedPairs);
    }

    private function escape(mixed $value): ?string
    {
        if (!is_string($value)) {
            if (is_scalar($value)) {
                // Bool/int/float ~ short-circuit additional checks
                return var_export($value, true);
            } elseif ($value instanceof Stringable) {
                $value = (string)$value;
            } else {
                // Array or non-stringable object. The PSR spec doesn't define
                // this clearly, just to be "as [lenient] as possible".
                return null;
            }
        }

        $needsEscaping = false;
        foreach (self::ESCAPED_CHARACTERS as $character) {
            if (str_contains(needle: $character, haystack: $value)) {
                $needsEscaping = true;
                break;
            }
        }

        if ($needsEscaping) {
            // Logfmt doesn't seem to really specify escaping in any real
            // detail, but in practice this seems to work.
            return json_encode($value, JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
        } else {
            // Single-word strings don't get quoted
            return $value;
        }
    }
}
