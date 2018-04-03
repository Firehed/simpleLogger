<?php
declare(strict_types=1);

namespace Firehed\SimpleLogger;

/**
 * @coversDefaultClass Firehed\SimpleLogger\Stdout
 * @covers ::<protected>
 * @covers ::<private>
 */
class StdoutTest extends \PHPUnit\Framework\TestCase
{
    use BaseTestTrait;

    public function getLogger()
    {
        return $this->getMockBuilder(Stdout::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @covers ::__construct
     */
    public function testDefaultConstrutor()
    {
        $this->assertInstanceOf(Stdout::class, new Stdout());
    }
}
