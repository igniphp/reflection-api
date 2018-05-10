<?php declare(strict_types=1);

namespace IgniTest\Unit\Utils\ReflectionApi;

use Igni\Utils\ReflectionApi\RuntimeProperty;
use PHPUnit\Framework\TestCase;

class RuntimePropertyTest extends TestCase
{
    public function testCanInstantiate(): void
    {
        $property = new RuntimeProperty('testA');

        self::assertInstanceOf(RuntimeProperty::class, $property);
        self::assertSame('testA', $property->getName());
    }

    public function testMakeStatic(): void
    {
        $property = new RuntimeProperty('testA');
        $property->makeStatic();

        self::assertTrue($property->isStatic());
        self::assertSame('public static $testA;', $property->generateCode());
    }
}
