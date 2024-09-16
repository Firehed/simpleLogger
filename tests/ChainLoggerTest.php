<?php

declare(strict_types=1);

namespace Firehed\SimpleLogger;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel as LL;
use Psr\Log\LoggerInterface;
use TypeError;
use stdClass;

#[CoversClass(ChainLogger::class)]
#[Small]
class ChainLoggerTest extends \PHPUnit\Framework\TestCase
{
    use BaseTestTrait;

    /** @var mixed[][] */
    private $logs = [];

    public function getLogger(): Base
    {
        return new ChainLogger();
    }

    public function testLoggerInConstruct(): void
    {
        $this->assertInstanceOf(
            ChainLogger::class,
            new ChainLogger([
                $this->makeMockLogger(),
            ])
        );
    }

    public function testNonLoggerInConstruct(): void
    {
        $this->expectException(TypeError::class);
        // @phpstan-ignore-next-line
        new ChainLogger([new stdClass()]);
    }

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
            ->willReturnCallback(function (...$args) use ($soh) {
                $this->logs[$soh][] = $args;
            });
        return $mock;
    }
}
