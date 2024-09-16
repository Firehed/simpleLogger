<?php

declare(strict_types=1);

namespace Firehed\SimpleLogger;

use Psr\Log\LogLevel;
use Stringable;

interface FormatterInterface
{
    /**
     * @param LogLevel::* $level
     * @param array<string, mixed> $context
     */
    public function format(string $level, string|Stringable $message, array $context = []): string;
}
