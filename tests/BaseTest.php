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
    private FormatterInterface&MockObject $formatter;

    public function setUp(): void
    {
        $this->formatter = self::createMock(FormatterInterface::class);
        $this->logger = new class ($this->formatter) extends Base {
            public ?string $written = null;
            public function __construct(FormatterInterface $formatter)
            {
                $this->formatter = $formatter;
            }
            protected function write(string $level, string $message): void
            {
                $this->written = $message;
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

    public function testFormatterIsApplied(): void
    {
        $this->formatter->expects(self::once())
            ->method('format')
            ->with(LL::NOTICE, 'message', ['a' => 'b'])
            ->willReturn('msg=message a=b');
        $this->logger->notice('message', ['a' => 'b']);
        self::assertSame('msg=message a=b', $this->logger->written); // @phpstan-ignore-line
    }

    /**
     * @param LL::* $atLevel
     * @param array<LL::*> $shouldLog Levels which should be logged
     */
    #[DataProvider('levelFiltering')]
    public function testFiltering(string $atLevel, array $shouldLog): void
    {
        $this->logger->setLevel($atLevel);
        foreach (self::allLevels() as $levelDP) {
            list($level) = $levelDP;
            $this->logger->written = null; // @phpstan-ignore-line
            $this->logger->log($level, 'someMessage');
            if (in_array($level, $shouldLog)) {
                $this->assertNotNull(
                    $this->logger->written, // @phpstan-ignore-line
                    "$level should have logged at $atLevel but did not"
                );
            } else {
                $this->assertNull(
                    $this->logger->written, // @phpstan-ignore-line
                    "$level should not have logged at $atLevel but did"
                );
            }
        }
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
}
