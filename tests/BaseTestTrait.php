<?php

declare(strict_types=1);

namespace Firehed\SimpleLogger;

use Psr\Log\LogLevel;
use Psr\Log\LoggerInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use RuntimeException;

/**
 * Provides some very basic tests for "this shouldn't crash"
 */
trait BaseTestTrait
{
    use LogLevelsTrait;

    abstract protected function getLogger(): Base;

    public function testIsLogger(): void
    {
        $logger = $this->getLogger();
        $this->assertInstanceOf(LoggerInterface::class, $logger);
        $this->assertInstanceOf(ConfigurableLoggerInterface::class, $logger);
    }

    #[DataProvider('allLevels')]
    public function testSimpleWriteViaDirect(string $level): void
    {
        $this->assertNull(
            $this->getLogger()->{$level}('Some message')
        );
    }

    /**
     * @param LogLevel::* $level
     */
    #[DataProvider('allLevels')]
    #[DoesNotPerformAssertions]
    public function testSimpleWriteViaLog(string $level): void
    {
        $this->getLogger()->log($level, 'Some message');
    }

    /**
     * @param LogLevel::* $level
     */
    #[DataProvider('allLevels')]
    #[DoesNotPerformAssertions]
    public function testInterpolatedMessageAtAllLevels(string $level): void
    {
        $this->getLogger()->log(
            $level,
            'Message with {format}',
            ['format' => 'a placeholder']
        );
    }

    public function testExeptionInterpolation(): void
    {
        $e = new RuntimeException('Some error');
        $this->getLogger()->log('error', 'Some message', ['exception' => $e]);
        $this->markTestIncomplete('Need to validate presence/non-presence of exception based on config');
    }

    // TODO: add complex interpolated types, similar to the
    // Psr\Log\Test\LoggerInterfaceTest
}
