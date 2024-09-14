<?php

declare(strict_types=1);

namespace Firehed\SimpleLogger;

/**
 * @coversDefaultClass Firehed\SimpleLogger\Syslog
 */
class SyslogTest extends \PHPUnit\Framework\TestCase
{
    use BaseTestTrait;

    public function getLogger(): Base
    {
        return new Syslog();
    }
}
