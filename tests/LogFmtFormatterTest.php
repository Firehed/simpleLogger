<?php

declare(strict_types=1);

namespace Firehed\SimpleLogger;

use DateTimeImmutable;
use Firehed\Clock\Clock;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;

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

    public function testValueCoercion(): void
    {
        $good = new class {
            public function __toString(): string
            {
                return 'good';
            }
        };
        $bad = new stdClass();

        $lf = new LogFmtFormatter(
            levelKey: null,
            timestampKey: null,
        );
        $message = $lf->format('notice', '', [
            'a' => 'b',
            'duration_ms' => 42,
            'worked' => true,
            'failed' => false,
            'stringy' => $good,
            'not_stringy' => $bad,
            'skip' => [],
        ]);

        self::assertStringContainsString('a=b', $message);
        self::assertStringContainsString('duration_ms=42', $message);
        self::assertStringContainsString('worked=true', $message);
        self::assertStringContainsString('failed=false', $message);
        self::assertStringContainsString('stringy=good', $message);

        self::assertStringNotContainsString('not_stringy=', $message);
        self::assertStringNotContainsString('skip=', $message);
        self::assertStringNotContainsString('msg=', $message);
    }
}
