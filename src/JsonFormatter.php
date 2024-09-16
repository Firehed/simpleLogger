<?php

declare(strict_types=1);

namespace Firehed\SimpleLogger;

use Stringable;

class JsonFormatter
{
    public function format(string $level, string|Stringable $message, array $context = []): string
    {
        // $message = (string)$message;
        // Intrepolate context into message
        // return json_encode([
        // 'level' => $level,
        // 'message' => $message,
        // ...$remainingContext,
        // ]);
    }
}
