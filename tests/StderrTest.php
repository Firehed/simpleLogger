<?php

declare(strict_types=1);

namespace Firehed\SimpleLogger;

/**
 * @coversDefaultClass Firehed\SimpleLogger\Stderr
 */
class StderrTest extends \PHPUnit\Framework\TestCase
{
    use BaseTestTrait;

    public function getLogger(): Base
    {
        return $this->getMockBuilder(Stderr::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @covers ::__construct
     */
    public function testDefaultConstrutor(): void
    {
        $this->assertInstanceOf(Stderr::class, new Stderr());
    }
}
