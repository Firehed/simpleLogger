<?php

declare(strict_types=1);

namespace Firehed\SimpleLogger;

/**
 * @coversDefaultClass Firehed\SimpleLogger\Stdout
 */
class StdoutTest extends \PHPUnit\Framework\TestCase
{
    use BaseTestTrait;

    public function getLogger(): Base
    {
        return $this->getMockBuilder(Stdout::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @covers ::__construct
     */
    public function testDefaultConstrutor(): void
    {
        $this->assertInstanceOf(Stdout::class, new Stdout());
    }
}
