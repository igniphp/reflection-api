<?php declare(strict_types=1);

namespace Igni\Utils\Exception;

use Igni\Exception\RuntimeException;

class ReflectionApiException extends RuntimeException
{
    public static function forNonExistentClass(string $class): self
    {
        return new self("Class {$class} does not exists or was not setup for autoload.");
    }

    public static function forNonExistentTrait(string $trait): self
    {
        return new self("Trait {$trait} does not exists or was not setup for autoload.");
    }

    public static function forNonExistentInterface(string $interface): self
    {
        return new self("Interface {$interface} does not exists or was not setup for autoload.");
    }

    public static function forAlreadyDefinedClass(string $class): self
    {
        return new self("Class {$class} is already defined.");
    }

    public static function forInstantiationFailure(string $class, string $reason = ''): self
    {
        return new self("Could not instantiate class ${class}.\n${reason}");
    }

    public static function forAbstractMethodWithBody(string $method): self
    {
        return new self("Method ${method} is marked as static thus it should not contain any body.");
    }

    public static function forFinalAbstract(): self
    {
        return new self("Expression cannot be final and abstract at the same time.");
    }
}
