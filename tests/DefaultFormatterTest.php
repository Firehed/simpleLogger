<?php

declare(strict_types=1);

namespace Firehed\SimpleLogger;

use LogicException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;
use RuntimeException;

#[CoversClass(DefaultFormatter::class)]
#[Small]
class DefaultFormatterTest extends TestCase
{
    private DefaultFormatter $formatter;

    public function setUp(): void
    {
        $this->formatter = new DefaultFormatter();
    }

    public function testSetFormat(): void
    {
        $this->formatter->setFormat('[{level}] %s');
        $result = $this->formatter->format(LogLevel::DEBUG, 'message {a}', ['a' => 'b']);
        self::assertSame('[debug] message b', $result);
    }

    #[DoesNotPerformAssertions]
    public function testSetDateFormat(): void
    {
        $this->formatter->setDateFormat('%Y-%m-%d');
        // Doesn't support clock. this can't be tested well :/
    }

    public function testSetFormatFailsIfPlaceholderIsMissing(): void
    {
        $this->expectException(LogicException::class);
        $this->formatter->setFormat('[{level}] oops no percent s');
    }

    public function testExceptionRendering(): void
    {
        $this->formatter->setRenderExceptions(true);
        $ex = new RuntimeException('test message');
        $formatted = $this->formatter->format(LogLevel::ERROR, '', ['exception' => $ex]);
        self::assertStringContainsString('[error]', $formatted);
        self::assertStringContainsString('RuntimeException: test message', $formatted);
        self::assertStringContainsString('Stack trace:', $formatted);
    }
}
