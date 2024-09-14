<?php

declare(strict_types=1);

namespace Firehed\SimpleLogger;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel as LL;
use stdClass;
use TypeError;

/**
 * @coversDefaultClass Firehed\SimpleLogger\ChainLogger
 */
class ChainLoggerTest extends \PHPUnit\Framework\TestCase
{
    use BaseTestTrait;

    /** @var mixed[][] */
    private $logs = [];

    public function getLogger(): Base
    {
        return new ChainLogger();
    }

    /**
     * @covers ::__construct
     */
    public function testLoggerInConstruct(): void
    {
        $this->assertInstanceOf(
            ChainLogger::class,
            new ChainLogger([
                $this->makeMockLogger(),
            ])
        );
    }

    /**
     * @covers ::__construct
     */
    public function testNonLoggerInConstruct(): void
    {
        $this->expectException(TypeError::class);
        // @phpstan-ignore-next-line
        new ChainLogger([new stdClass()]);
    }

    /**
     * @covers ::addLogger
     */
    public function testWithOneLogger(): void
    {
        $chain = new ChainLogger();
        $mock = $this->makeMockLogger();
        $soh = spl_object_hash($mock);
        $chain->addLogger($mock);

        $chain->debug('message');

        $this->assertSame([
            $soh => [
                [LL::DEBUG, 'message', []],
            ],
        ], $this->logs);
    }

    public function testWithMultipleLoggers(): void
    {
        $chain = new ChainLogger();

        $mock1 = $this->makeMockLogger();
        $soh1 = spl_object_hash($mock1);
        $chain->addLogger($mock1);

        $mock2 = $this->makeMockLogger();
        $soh2 = spl_object_hash($mock2);
        $chain->addLogger($mock2);

        $chain->debug('message');

        $expected = [
            $soh1 => [
                [LL::DEBUG, 'message', []],
            ],
            $soh2 => [
                [LL::DEBUG, 'message', []],
            ],
        ];
        ksort($expected);
        ksort($this->logs);
        $this->assertSame($expected, $this->logs);
    }

    private function makeMockLogger(): LoggerInterface
    {
        $mock = $this->createMock(LoggerInterface::class);
        $soh = spl_object_hash($mock);
        $this->logs[$soh] = [];
        $mock->method('log')
            ->will($this->returnCallback(function (...$args) use ($soh) {
                $this->logs[$soh][] = $args;
            }));
        return $mock;
    }
}
