<?php

declare(strict_types=1);

namespace Firehed\SimpleLogger;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;

#[CoversClass(LevelFilter::class)]
#[Small]
class LevelFilterTest extends TestCase
{
    /**
     * @return array{LogLevel::*, LogLevel::*|null, bool}[]
     */
    public static function vectors(): array
    {
        return [
            'no filter emerg' => [LogLevel::EMERGENCY, null, true],
            'no filter debug' => [LogLevel::DEBUG, null, true],
            'severe on debug' => [LogLevel::ALERT, LogLevel::DEBUG, true],
            'debug on debug' => [LogLevel::DEBUG, LogLevel::DEBUG, true],
            'debug on info' => [LogLevel::DEBUG, LogLevel::INFO, false],
            'info on info' => [LogLevel::INFO, LogLevel::INFO, true],
            // Filtering "lower"
            'emergency filters alert' => [LogLevel::ALERT, LogLevel::EMERGENCY, false],
            'alert filters critical' =>  [LogLevel::CRITICAL, LogLevel::ALERT, false],
            'critical filters error' => [LogLevel::ERROR, LogLevel::CRITICAL, false],
            'error filters warning' => [LogLevel::WARNING, LogLevel::ERROR, false],
            'warning filters notice' => [LogLevel::NOTICE, LogLevel::WARNING, false],
            'notice filters info' => [LogLevel::INFO, LogLevel::NOTICE, false],
            'info filters debug' => [LogLevel::DEBUG, LogLevel::INFO, false],
        ];
    }

    #[DataProvider('vectors')]
    public function testFiltering(string $messageLevel, ?string $filter, bool $expected): void
    {
        self::assertSame($expected, LevelFilter::shouldLog(messageLevel: $messageLevel, minimumLevel: $filter));
    }
}
