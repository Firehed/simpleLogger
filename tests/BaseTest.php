<?php

declare(strict_types=1);

namespace Firehed\SimpleLogger;

use LogicException;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel as LL;

/**
 * @coversDefaultClass Firehed\SimpleLogger\Base
 * @covers ::<protected>
 * @covers ::<private>
 */
class BaseTest extends \PHPUnit\Framework\TestCase
{
    use LogLevelsTrait;

    /** @var Base | \PHPUnit\Framework\MockObject\MockObject */
    private $logger;

    /** @var bool */
    private $wrote = false;

    public function setUp(): void
    {
        $this->logger = $this->getMockForAbstractClass(Base::class);

        $this->logger->method('writeLog')
            ->will($this->returnCallback(function () {
                $this->wrote = true;
            }));
    }

    /**
     * @covers ::getLevel
     */
    public function testDefaultLevelIsDebug(): void
    {
        $this->assertSame(LL::DEBUG, $this->logger->getLevel());
    }

    /**
     * @covers ::getLevel
     * @covers ::setLevel
     */
    public function testSetLevel(): void
    {
        $this->assertNotSame(LL::EMERGENCY, $this->logger->getLevel());
        $this->logger->setLevel(LL::EMERGENCY);
        $this->assertSame(LL::EMERGENCY, $this->logger->getLevel());
    }

    /**
     * @covers ::log
     * @dataProvider levelFiltering
     * @param array<LL::*> $shouldLog Levels which should be logged
     */
    public function testFiltering(string $atLevel, array $shouldLog): void
    {
        $this->logger->setLevel($atLevel);
        foreach (self::allLevels() as $levelDP) {
            list($level) = $levelDP;
            $this->wrote = false;
            $this->logger->log($level, 'someMessage');
            if (in_array($level, $shouldLog)) {
                $this->assertTrue(
                    $this->wrote,
                    "$level should have logged at $atLevel but did not"
                );
            } else {
                $this->assertFalse(
                    $this->wrote,
                    "$level should not have logged at $atLevel but did"
                );
            }
        }
    }

    /** @covers ::setFormat */
    public function testSetFormat(): void
    {
        // @phpstan-ignore-next-line
        $this->assertNull($this->logger->setFormat('[{level}] %s'));
    }

    /** @covers ::setDateFormat */
    public function testSetDateFormat(): void
    {
        // @phpstan-ignore-next-line
        $this->assertNull($this->logger->setDateFormat('%Y-%m-%d'));
    }

    /** @covers ::setFormat */
    public function testSetFormatFailsIfPlaceholderIsMissing(): void
    {
        $this->expectException(LogicException::class);
        $this->logger->setFormat('[{level}] oops no percent s');
    }

    /**
     * @return array<LL::*|array<LL::*>>[]
     */
    public function levelFiltering(): array
    {
        return [
            [
                LL::EMERGENCY,
                [LL::EMERGENCY]
            ],
            [
                LL::ALERT,
                [LL::EMERGENCY, LL::ALERT]
            ],
            [
                LL::CRITICAL,
                [LL::EMERGENCY, LL::ALERT, LL::CRITICAL]
            ],
            [
                LL::ERROR,
                [LL::EMERGENCY, LL::ALERT, LL::CRITICAL, LL::ERROR]
            ],
            [
                LL::WARNING,
                [LL::EMERGENCY, LL::ALERT, LL::CRITICAL, LL::ERROR, LL::WARNING]
            ],
            [
                LL::NOTICE,
                [LL::EMERGENCY, LL::ALERT, LL::CRITICAL, LL::ERROR, LL::WARNING, LL::NOTICE]
            ],
            [
                LL::INFO,
                [LL::EMERGENCY, LL::ALERT, LL::CRITICAL, LL::ERROR, LL::WARNING, LL::NOTICE, LL::INFO]
            ],
            [
                LL::DEBUG,
                [LL::EMERGENCY, LL::ALERT, LL::CRITICAL, LL::ERROR, LL::WARNING, LL::NOTICE, LL::INFO, LL::DEBUG]
            ],
        ];
    }

    /**
     * @return array<string|int>[]
     */
    public function syslogMap(): array
    {
        return [
            [LL::EMERGENCY, LOG_EMERG],
            [LL::ALERT, LOG_ALERT],
            [LL::CRITICAL, LOG_CRIT],
            [LL::ERROR, LOG_ERR],
            [LL::WARNING, LOG_WARNING],
            [LL::NOTICE, LOG_NOTICE],
            [LL::INFO, LOG_INFO],
            [LL::DEBUG, LOG_DEBUG],
        ];
    }
}
