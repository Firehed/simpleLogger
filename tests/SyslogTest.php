<?php
declare(strict_types=1);

namespace Firehed\SimpleLogger;

/**
 * @coversDefaultClass Firehed\SimpleLogger\Syslog
 * @covers ::<protected>
 * @covers ::<private>
 */
class SyslogTest extends \PHPUnit\Framework\TestCase
{
    use BaseTestTrait;

    public function getLogger()
    {
        return new Syslog();
    }
}
