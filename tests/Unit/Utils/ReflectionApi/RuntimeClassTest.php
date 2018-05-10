<?php declare(strict_types=1);

namespace IgniTest\Unit\Utils\ReflectionApi;

use Igni\Utils\Exception\ReflectionApiException;
use Igni\Utils\ReflectionApi\RuntimeClass;
use Igni\Utils\ReflectionApi\RuntimeConstant;
use Igni\Utils\ReflectionApi\RuntimeMethod;
use Igni\Utils\ReflectionApi\RuntimeProperty;
use IgniTest\Fixtures\AInterface;
use PHPUnit\Framework\TestCase;

class RuntimeClassTest extends TestCase
{
    public function testCanInstantiate(): void
    {
        self::assertInstanceOf(RuntimeClass::class, new RuntimeClass('TestClass'));
    }

    public function testGetClassNameAndNamespace(): void
    {
        $class = new RuntimeClass('Namespace\\TestClass');

        self::assertSame('TestClass', $class->getClassName());
        self::assertSame('Namespace', $class->getNamespace());
        self::assertSame('Namespace\\TestClass', $class->getClass());
    }

    public function testAddMethod(): void
    {
        $class = new RuntimeClass('TestClassA');

        $class->addMethod(
            (new RuntimeMethod('doSomething'))
                ->setReturnType('string')
                ->makePublic()
                ->setBody(
                    'return $this->something'
                )
        );

        self::assertSame("\tpublic function doSomething(): string", explode(PHP_EOL, $class->generateCode())[2]);
        self::assertTrue($class->hasMethod('doSomething'));
    }

    public function testAddProperty(): void
    {
        $class = new RuntimeClass('TestClassB');

        $class->addProperty((new RuntimeProperty('propertyXyz'))->makePrivate());

        self::assertSame("\tprivate \$propertyXyz;", explode(PHP_EOL, $class->generateCode())[2]);
        self::assertTrue($class->hasProperty('propertyXyz'));
    }

    public function testCreateClassWithNamespace(): void
    {
        $class = new RuntimeClass('Some\\Namespace\\TestClassB');

        self::assertSame('Some\\Namespace', $class->getNamespace());
        self::assertSame('namespace Some\\Namespace {', explode(PHP_EOL, $class->generateCode())[0]);
    }

    public function testImplements(): void
    {
        $class = new RuntimeClass('TestClassC');
        $class->implements(AInterface::class);

        self::assertSame('class TestClassC implements ' . AInterface::class, explode(PHP_EOL, $class->generateCode())[0]);
        self::assertTrue($class->isImplementing(AInterface::class));
    }

    public function testExtends(): void
    {
        $class = new RuntimeClass('TestClassD');
        $class->extends(\stdClass::class);

        self::assertSame('class TestClassD extends stdClass', explode(PHP_EOL, $class->generateCode())[0]);
        self::assertTrue($class->isExtending(\stdClass::class));
    }

    public function testAddConstant(): void
    {
        $class = new RuntimeClass('TestClassE');
        $class->addConstant(new RuntimeConstant('TEST_CONSTANT', 1));

        self::assertSame("\tpublic const TEST_CONSTANT = 1;", explode(PHP_EOL, $class->generateCode())[2]);
        self::assertTrue($class->hasConstant('TEST_CONSTANT'));
    }

    public function testFinalClass(): void
    {
        $class = new RuntimeClass('TestClassD');
        $class->makeFinal();

        self::assertSame('final class TestClassD', explode(PHP_EOL, $class->generateCode())[0]);
        self::assertTrue($class->isFinal());
    }

    public function testFinalFailsWhenAbstract(): void
    {
        $this->expectException(ReflectionApiException::class);
        $class = new RuntimeClass('TestClassD');
        $class->makeFinal();
        $class->makeAbstract();
    }

    public function testAbstractFailsWhenFinal(): void
    {
        $this->expectException(ReflectionApiException::class);
        $class = new RuntimeClass('TestClassD');
        $class->makeAbstract();
        $class->makeFinal();
    }

    public function testAbstractClass(): void
    {
        $class = new RuntimeClass('TestClassE');
        $class->makeAbstract();

        self::assertSame('abstract class TestClassE', explode(PHP_EOL, $class->generateCode())[0]);
        self::assertTrue($class->isAbstract());
    }

    public function testLoad(): void
    {
        $class = new RuntimeClass('LoadedClass');

        self::assertTrue($class->load());
        self::assertTrue(class_exists('LoadedClass'));
    }

    public function testCreateInstance(): void
    {
        $class = new RuntimeClass('TestClassABC');
        $class->implements(AInterface::class);
        $doSomething = new RuntimeMethod('doSomething');
        $doSomething->setReturnType('string');
        $doSomething->setBody(
            'return "test";'
        );
        $class->addMethod($doSomething);

        $instance = $class->createInstance();

        self::assertInstanceOf('TestClassABC', $instance);
        self::assertSame('test', $instance->doSomething());
    }
}
