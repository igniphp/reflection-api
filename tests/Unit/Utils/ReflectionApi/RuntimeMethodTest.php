<?php declare(strict_types=1);

namespace IgniTest\Unit\Utils\ReflectionApi;

use Igni\Utils\ReflectionApi\RuntimeArgument;
use Igni\Utils\ReflectionApi\RuntimeMethod;
use PHPUnit\Framework\TestCase;

class RuntimeMethodTest extends TestCase
{
    public function testCanInstantiate(): void
    {
        $instance = new RuntimeMethod('doSomething');
        self::assertInstanceOf(RuntimeMethod::class, $instance);
        self::assertSame('doSomething', $instance->getName());
    }

    public function testSetReturnType(): void
    {
        $method = new RuntimeMethod('doSomething');
        $method->setReturnType('void');

        self::assertSame('void', $method->getReturnType());
        self::assertContains('function doSomething(): void', explode(PHP_EOL, $method->generateCode())[0]);
    }

    public function testAddArgument(): void
    {
        $method = new RuntimeMethod('doSomething');
        $method->addArgument(new RuntimeArgument('argument', 'array'));

        self::assertSame("public function doSomething(array \$argument)\n{\n}", $method->generateCode());
    }

    public function testAddMultipleArguments(): void
    {
        $method = new RuntimeMethod('doSomething');
        $method->addArgument(new RuntimeArgument('argument', 'array'));
        $method->addArgument(new RuntimeArgument('name', 'string'));

        self::assertSame("public function doSomething(array \$argument, string \$name)\n{\n}", $method->generateCode());
    }

    public function testSetBody(): void
    {
        $method = new RuntimeMethod('doSomething');
        $method->setBody(
            '$result = $a + $b',
            'return $result'
        );

        self::assertSame(
            "public function doSomething()\n{\n\t\$result = \$a + \$b;\n\treturn \$result;\n}",
            $method->generateCode()
        );
    }

    public function testCreatePrivateMethod(): void
    {
        $method = new RuntimeMethod('doSomething');
        $method->makePrivate();

        self::assertTrue($method->isPrivate());
        self::assertFalse($method->isProtected());
        self::assertFalse($method->isPublic());
        self::assertSame('private', $method->getVisibility());

        self::assertSame("private function doSomething()",
            explode(PHP_EOL, $method->generateCode())[0]
        );
    }

    public function testAddExpression(): void
    {

    }
}
