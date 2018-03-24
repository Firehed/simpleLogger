<?php
declare(strict_types=1);

namespace SimpleLogger;

/**
 * @coversDefaultClass SimpleLogger\Syslog
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
