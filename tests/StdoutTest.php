<?php

declare(strict_types=1);

namespace Firehed\SimpleLogger;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(Stdout::class)]
#[Small]
class StdoutTest extends \PHPUnit\Framework\TestCase
{
    use BaseTestTrait;

    public function getLogger(): Base
    {
        return $this->getMockBuilder(Stdout::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testDefaultConstrutor(): void
    {
        $this->assertInstanceOf(Stdout::class, new Stdout());
    }
}
