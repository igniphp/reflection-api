<?php declare(strict_types=1);

namespace Igni\Utils;

use Closure;
use Igni\Utils\Exception\ReflectionApiException;
use Igni\Utils\ReflectionApi\RuntimeClass;
use ReflectionClass;
use ReflectionFunction;
use ReflectionMethod;
use Throwable;

final class ReflectionApi
{
    /**
     * @var ReflectionClass[]
     */
    private static $reflections = [];

    /**
     * @var object[]
     */
    private static $instances = [];

    /**
     * Creates instance of the given class.
     *
     * @param string $class
     * @return object
     */
    public static function createInstance(string $class)
    {
        if (!isset(self::$instances[$class])) {
            if (!class_exists($class)) {
                throw ReflectionApiException::forNonExistentClass($class);
            }
            try {
                self::$instances[$class] = unserialize(sprintf('O:%d:"%s":0:{}', strlen($class), $class));
            } catch (Throwable $exception) {
                throw ReflectionApiException::forInstantiationFailure($class, $exception->getMessage());
            }
        }

        return clone self::$instances[$class];
    }

    public static function createClass(string $class): RuntimeClass
    {
        return new RuntimeClass($class);
    }

    public static function getClosureBody(Closure $closure): string
    {
        $reflection = self::reflectFunction($closure);
        $lines = file($reflection->getFileName());
        $body = '';
        for($l = $reflection->getStartLine() - 1; $l < $reflection->getEndLine(); $l++) {
            $body .= $lines[$l];
        }

        preg_match('#function\([^{].*?\{(.*)\}#is', $body, $matches);

        return trim($matches[1]);
    }

    /**
     * Overrides object's property value.
     *
     * @param $instance
     * @param string $property property name
     * @param $value
     */
    public static function writeProperty($instance, string $property, $value): void
    {
        static $writer;
        if ($writer === null) {
            $writer = function ($name, $value) {
                $this->$name = $value;
            };
        }

        $set = Closure::bind($writer, $instance, $instance);
        $set($property, $value);
    }

    /**
     * @param $instance
     * @param string $property
     * @return mixed
     */
    public static function readProperty($instance, string $property)
    {
        static $reader;
        if ($reader === null) {
            $reader = function ($name) {
                return $this->$name ?? null;
            };
        }

        $read = Closure::bind($reader, $instance, $instance);

        return $read($property);
    }

    /**
     * Creates and caches in memory reflection of the given class.
     *
     * @param string $className
     * @return ReflectionClass
     * @throws \ReflectionException
     */
    public static function reflectClass(string $className): ReflectionClass
    {
        if (isset(self::$reflections[$className])) {
            return self::$reflections[$className];
        }

        return self::$reflections[$className] = new ReflectionClass($className);
    }

    /**
     * Creates and caches in memory reflection of the given function.
     *
     * @param $function
     * @return ReflectionFunction
     * @throws \ReflectionException
     */
    public static function reflectFunction($function): ReflectionFunction
    {
        if (!is_string($function)) {
            return new ReflectionFunction($function);
        }

        if (isset(self::$reflections[$function])) {
            return self::$reflections[$function];
        }

        return self::$reflections[$function] = new ReflectionFunction($function);
    }

    /**
     * Creates and caches in memory reflection of the given method.
     *
     * @param string $class
     * @param string $method
     * @return ReflectionMethod
     * @throws \ReflectionException
     */
    public static function reflectMethod(string $class, string $method): ReflectionMethod
    {
        $class = self::reflectClass($class);

        return $class->getMethod($method);
    }
}
