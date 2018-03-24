<?php
declare(strict_types=1);

namespace SimpleLogger;

use Psr\Log\LoggerInterface;

/**
 * Provides some very basic tests for "this shouldn't crash"
 */
trait BaseTestTrait
{
    use LogLevelsTrait;

    abstract protected function getLogger(): Base;

    /**
     * @covers ::__construct
     */
    public function testIsLogger()
    {
        $this->assertInstanceOf(LoggerInterface::class, $this->getLogger());
    }

    /**
     * @covers ::log
     * @dataProvider allLevels
     */
    public function testSimpleWriteViaDirect($level)
    {
        $this->assertNull(
            $this->getLogger()->{$level}('Some message')
        );
    }

    /**
     * @covers ::log
     * @dataProvider allLevels
     */
    public function testSimpleWriteViaLog($level)
    {
        $this->assertNull(
            $this->getLogger()->log($level, 'Some message')
        );
    }

    /**
     * @covers ::log
     * @dataProvider allLevels
     */
    public function testInterpolatedMessageAtAllLevels($level)
    {
        $this->assertNull(
            $this->getLogger()->log(
                $level,
                'Message with {format}',
                ['format' => 'a placeholder']
            )
        );
    }

    // TODO: add complex interpolated types, similar to the
    // Psr\Log\Test\LoggerInterfaceTest
}
