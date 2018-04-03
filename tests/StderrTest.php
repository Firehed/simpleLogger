<?php
declare(strict_types=1);

namespace Firehed\SimpleLogger;

/**
 * @coversDefaultClass Firehed\SimpleLogger\Stderr
 * @covers ::<protected>
 * @covers ::<private>
 */
class StderrTest extends \PHPUnit\Framework\TestCase
{
    use BaseTestTrait;

    public function getLogger()
    {
        return $this->getMockBuilder(Stderr::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @covers ::__construct
     */
    public function testDefaultConstrutor()
    {
        $this->assertInstanceOf(Stderr::class, new Stderr());
    }
}
