<?php declare(strict_types=1);

namespace IgniTest\Unit\Utils\ReflectionApi;

use Igni\Utils\ReflectionApi\RuntimeArgument;
use IgniTest\Fixtures\AInterface;
use PHPUnit\Framework\TestCase;

class RuntimeArgumentTest extends TestCase
{
    public function testCanInstantiate(): void
    {
        $argument = new RuntimeArgument('testA', 'string');
        self::assertInstanceOf(RuntimeArgument::class, $argument);
        self::assertSame('testA', $argument->getName());
        self::assertSame('string', $argument->getType());
    }

    public function testMakeVariadic(): void
    {
        $argument = new RuntimeArgument('testArgument');
        $argument->makeVariadic();

        self::assertTrue($argument->isVariadic());
        self::assertContains('...$testArgument', $argument->generateCode());
    }

    public function testSetArrayAsDefaultValue(): void
    {
        $value = [1, 'two', ['three']];
        $argument = new RuntimeArgument('testArgument');
        $argument->setDefaultValue($value);

        self::assertSame($value, $argument->getDefaultValue());
        self::assertTrue($argument->hasDefaultValue());
        self::assertContains(var_export($value, true), $argument->generateCode());
    }

    public function testSetType(): void
    {
        $argument = new RuntimeArgument('testArgument');
        $argument->setType(AInterface::class);

        self::assertSame(AInterface::class, $argument->getType());
        self::assertSame(AInterface::class . ' $testArgument', $argument->generateCode());
    }
}
