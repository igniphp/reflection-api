<?php declare(strict_types=1);

namespace IgniTest\Unit\Utils;

use Igni\Utils\Exception\ReflectionApiException;
use Igni\Utils\ReflectionApi;
use IgniTest\Fixtures\AClass;
use IgniTest\Fixtures\AInterface;
use IgniTest\Fixtures\TestAbstractClass;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Closure;

class ReflectionApiTest extends TestCase
{
    public function testCreateInstance(): void
    {
        $instance = ReflectionApi::createInstance(AClass::class);

        self::assertInstanceOf(AClass::class, $instance);
        self::assertEmpty($instance->doSomething());
    }

    public function testFailCreateInstanceFromAbstractClass(): void
    {
        $this->expectException(ReflectionApiException::class);
        ReflectionApi::createInstance(TestAbstractClass::class);
    }

    public function testFailCreateInstanceFromInterface(): void
    {
        $this->expectException(ReflectionApiException::class);
        ReflectionApi::createInstance(AInterface::class);
    }

    public function testWriteProperty(): void
    {
        $a = new AClass();
        ReflectionApi::writeProperty($a, 'something', 'Stress.');

        self::assertSame('Stress.', $a->doSomething());
    }

    public function testWriteNotDeclaredProperty(): void
    {
        $a = new AClass();
        ReflectionApi::writeProperty($a, 'no', 'Stress.');
        self::assertSame('Stress.', $a->no);
    }

    public function testReadProperty(): void
    {
        $a = new AClass('Nothing.');

        self::assertSame($a->doSomething(), ReflectionApi::readProperty($a, 'something'));
    }

    public function testReadNotDeclaredProperty(): void
    {
        $a = new AClass('Nothing.');

        self::assertNull(ReflectionApi::readProperty($a, 'no'));
    }

    public function testReflectClass(): void
    {
        $reflection = ReflectionApi::reflectClass(AClass::class);

        self::assertInstanceOf(ReflectionClass::class, $reflection);
        self::assertSame($reflection, ReflectionApi::reflectClass(AClass::class));
    }

    public function testCreateClass(): void
    {
        $class = ReflectionApi::createClass('TestClass');
        self::assertInstanceOf(ReflectionApi\RuntimeClass::class, $class);
    }

    public function testCreateClassForExistingClass(): void
    {
        $this->expectException(ReflectionApiException::class);
        ReflectionApi::createClass(AClass::class);
    }

    /**
     * @param Closure $closure
     * @param string $result
     * @dataProvider provideClosureResults
     */
    public function testDumpClosure(Closure $closure, string $result): void
    {
        self::assertSame($result, ReflectionApi::getClosureBody($closure));
    }

    public function provideClosureResults(): array
    {
        return [
            [
                function() {return 'test.';},
                "return 'test.';"
            ],
            [function() {
            return 'test';
            }, "return 'test';"],
            [
                function() { return function(){}; },
                'return function(){};'
            ]
        ];
    }
}
