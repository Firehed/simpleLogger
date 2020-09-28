<?php

declare(strict_types=1);

namespace Firehed\SimpleLogger;

use Psr\Log\LoggerInterface;

interface ConfigurableLoggerInterface extends LoggerInterface
{
    public function setFormat(string $format): void;

    public function setLevel(string $level): void;
}
