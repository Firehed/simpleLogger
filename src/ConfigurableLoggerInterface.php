<?php

declare(strict_types=1);

namespace Firehed\SimpleLogger;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * This interface is only intended to be checked by user code. It is not
 * intended for public implementation, and will not follow strict adherence to
 * semantic versioning. New methods may be added in minor versions.
 */
interface ConfigurableLoggerInterface extends LoggerInterface
{
    /**
     * @param LogLevel::* $level
     */
    public function setLevel(string $level): void;
}
