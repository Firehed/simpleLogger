<?php

declare(strict_types=1);

namespace Firehed\SimpleLogger;

use DateTimeImmutable;
use Firehed\Clock\Clock;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(LogFmtFormatter::class)]
#[Small]
class LogFmtFormatterTest extends TestCase
{
    public function testDefaultFormatting(): void
    {
        $now = new DateTimeImmutable();
        $clock = new Clock($now);
        $lf = new LogFmtFormatter(clock: $clock);

        $formatted = $lf->format('error', 'Page crashed during {thing}', [
            'thing' => 'foo',
            'exception' => new \Exception('asdf'),
            'duration' => '42ms',
        ]);

        self::assertStringContainsString('msg="Page crashed during foo"', $formatted);
        self::assertStringContainsString('duration=42ms', $formatted);
        self::assertStringContainsString('exception_type=Exception', $formatted);
        self::assertStringContainsString('exception_message=asdf', $formatted);
        self::assertStringContainsString('ts=' . $now->format(DateTimeImmutable::RFC3339), $formatted);
    }

    public function testStrippedFormatting(): void
    {
        $lf = new LogFmtFormatter(
            levelKey: null,
            timestampKey: null,
        );
        $msg = $lf->format('warning', 'Bad {thing} happened', [
            'thing' => 'news',
            'channel' => 'paper',
        ]);
        self::assertSame('msg="Bad news happened" channel=paper', $msg);
    }
}
