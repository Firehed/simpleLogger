<?php

declare(strict_types=1);

namespace Firehed\SimpleLogger;

/**
 * @coversDefaultClass Firehed\SimpleLogger\File
 * @covers ::<protected>
 * @covers ::<private>
 */
class FileTest extends \PHPUnit\Framework\TestCase
{
    use BaseTestTrait;

    public function getLogger(): Base
    {
        return new File('/dev/null');
    }
}
