<?php

declare(strict_types=1);

namespace Firehed\SimpleLogger;

use LogicException;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel as LL;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(Base::class)]
#[Small]
class BaseTest extends \PHPUnit\Framework\TestCase
{
    use LogLevelsTrait;

    private Base $logger;

    public function setUp(): void
    {
        $this->logger = new class extends Base {
            public bool $wrote = false;
            protected function writeLog($level, mixed $message, $context = []): void
            {
                $this->wrote = true;
            }
        };
    }

    public function testDefaultLevelIsDebug(): void
    {
        $this->assertSame(LL::DEBUG, $this->logger->getLevel());
    }

    public function testSetLevel(): void
    {
        $this->assertNotSame(LL::EMERGENCY, $this->logger->getLevel());
        $this->logger->setLevel(LL::EMERGENCY);
        $this->assertSame(LL::EMERGENCY, $this->logger->getLevel());
    }

    /**
     * @param array<LL::*> $shouldLog Levels which should be logged
     */
    #[DataProvider('levelFiltering')]
    public function testFiltering(string $atLevel, array $shouldLog): void
    {
        $this->logger->setLevel($atLevel);
        foreach (self::allLevels() as $levelDP) {
            list($level) = $levelDP;
            $this->logger->wrote = false; // @phpstan-ignore-line
            $this->logger->log($level, 'someMessage');
            if (in_array($level, $shouldLog)) {
                $this->assertTrue(
                    $this->logger->wrote, // @phpstan-ignore-line
                    "$level should have logged at $atLevel but did not"
                );
            } else {
                $this->assertFalse(
                    $this->logger->wrote, // @phpstan-ignore-line
                    "$level should not have logged at $atLevel but did"
                );
            }
        }
    }

    public function testSetFormat(): void
    {
        // @phpstan-ignore-next-line
        $this->assertNull($this->logger->setFormat('[{level}] %s'));
    }

    public function testSetDateFormat(): void
    {
        // @phpstan-ignore-next-line
        $this->assertNull($this->logger->setDateFormat('%Y-%m-%d'));
    }

    public function testSetFormatFailsIfPlaceholderIsMissing(): void
    {
        $this->expectException(LogicException::class);
        $this->logger->setFormat('[{level}] oops no percent s');
    }

    /**
     * @return array<LL::*|array<LL::*>>[]
     */
    public static function levelFiltering(): array
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
