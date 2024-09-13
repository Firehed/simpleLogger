<?php

declare(strict_types=1);

namespace Firehed\SimpleLogger;

use Psr\Log\AbstractLogger;
use Stringable;

class LogFmt extends AbstractLogger
{
    public function __construct(private ConfigurableLoggerInterface $writer)
    {
        $writer->setFormat('%s');
        $writer->setRenderExceptions(false); // We will self-manage this
    }

    public function log($level, string|Stringable $message, array $context = []): void
    {
        // Stringable to string
        if (!is_string($message)) {
            $message = $message->__toString();
        }

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
        $pairs['msg'] = $formattedMessage;
        $pairs['level'] = $level;

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
            $value = (string) $value;
            $hasSpaces = str_contains(needle: ' ', haystack: $value);
            $hasQuotes = str_contains(needle: '"', haystack: $value);
            if ($hasSpaces || $hasQuotes) {
                $escapedValue = json_encode($value, JSON_UNESCAPED_SLASHES);
            } else {
                $escapedValue = $value;
            }
            $formattedPairs[] = sprintf('%s=%s', $key, $escapedValue);
        }

        $final = implode(' ', $formattedPairs);

        $this->writer->log($level, $final);
    }
}
