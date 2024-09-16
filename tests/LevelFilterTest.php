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

        ];
    }

    #[DataProvider('vectors')]
    public function testFiltering(string $messageLevel, ?string $filter, bool $expected): void
    {
        self::assertSame($expected, LevelFilter::shouldLog(messageLevel: $messageLevel, minimumLevel: $filter));
    }
}
