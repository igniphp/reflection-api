<?php declare(strict_types=1);

namespace Igni\Utils\ReflectionApi;

use Igni\Utils\Exception\ReflectionApiException;
use Igni\Utils\ReflectionApi;

final class RuntimeClass implements CodeGenerator
{
    use FinalTrait;
    use AbstractTrait;

    /**
     * @var string
     */
    private $class;

    /**
     * @var string
     */
    private $className;

    /**
     * @var string[]
     */
    private $namespace;

    /**
     * @var string
     */
    private $extends;

    /**
     * @var string[]
     */
    private $implements = [];

    /**
     * @var string[]
     */
    private $uses = [];

    /**
     * @var bool
     */
    private $isLoaded = false;

    /**
     * @var RuntimeMethod[]
     */
    private $methods = [];

    /**
     * @var RuntimeProperty[]
     */
    private $properties = [];

    /**
     * @var RuntimeConstant[]
     */
    private $constant = [];

    /**
     * @var string[]
     */
    private static $registeredClasses = [];

    public function __construct(string $class, string ...$implements)
    {
        if (isset(self::$registeredClasses[$class]) || class_exists($class) || interface_exists($class)) {
            throw ReflectionApiException::forAlreadyDefinedClass($class);
        }

        $this->class = $class;
        $classParts = explode('\\', $class);
        $this->className = array_pop($classParts);
        $this->namespace = implode('\\', $classParts);

        $this->implements(...$implements);
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function implements(string ...$interfaces): self
    {
        foreach ($interfaces as &$interface) {
            if (!interface_exists($interface)) {
                throw ReflectionApiException::forNonExistentInterface($interface);
            }
            $interface = '\\' . $interface;
        }
        $this->implements = $interfaces;

        return $this;
    }

    public function extends(string $class): self
    {
        if (!class_exists($class)) {
            throw ReflectionApiException::forNonExistentClass($class);
        }
        $this->extends = '\\' . $class;

        return $this;
    }

    public function use(string ...$traits): self
    {
        foreach ($traits as &$trait) {
            if (!trait_exists($trait)) {
                throw ReflectionApiException::forNonExistentTrait($trait);
            }
            $trait = '\\' . $trait;
        }
        $this->uses = $traits;

        return $this;
    }

    public function isUsing(string $name): bool
    {
        return in_array('\\' . $name, $this->uses);
    }

    public function isExtending(string $name): bool
    {
        return ('\\' . $name) === $this->extends;
    }

    public function implementsInterface(string $interface): bool
    {
        return in_array('\\' . $interface, $this->implements);
    }

    public function addMethod(RuntimeMethod $method): self
    {
        $this->methods[$method->getName()] = $method;

        return $this;
    }

    public function hasMethod(string $name): bool
    {
        return isset($this->methods[$name]);
    }

    public function addProperty(RuntimeProperty $property): self
    {
        $this->properties[$property->getName()] = $property;

        return $this;
    }

    public function addConstant(RuntimeConstant $constant): self
    {
        $this->constant[$constant->getName()] = $constant;

        return $this;
    }

    public function hasConstant(string $name): bool
    {
        return isset($this->constant[$name]);
    }

    public function hasProperty(string $name): bool
    {
        return isset($this->properties[$name]);
    }

    public function generateCode(): string
    {
        $code = '';
        if ($this->namespace) {
            $code .= "namespace {$this->namespace} {\n";
        }

        if ($this->final) {
            $code .= 'final ';
        }

        if ($this->abstract) {
            $code .= 'abstract ';
        }

        $code .= "class {$this->className}";

        if ($this->extends) {
            $code .= " extends {$this->extends}";
        }

        if ($this->implements) {
            $code .= ' implements ' . implode(',', $this->implements);
        }

        $code .= "\n{\n";

        foreach ($this->constant as $constant) {
            $code .= "\t{$constant->generateCode()}\n";
        }

        foreach ($this->properties as $property) {
            $code .= "\t{$property->generateCode()}\n";
        }

        foreach ($this->methods as $method) {
            $methodBody = explode("\n", $method->generateCode());
            foreach ($methodBody as $methodLine) {
                $code .= "\t" . $methodLine . PHP_EOL;
            }
        }

        $code .= "}\n";

        if ($this->namespace) {
            $code .= "\n}\n";
        }

        return $code;
    }

    public function createInstance()
    {
        $this->load();

        return ReflectionApi::createInstance($this->class);
    }

    public function register(): bool
    {
        if (isset(self::$registeredClasses[$this->class])) {
            return false;
        }

        self::$registeredClasses[$this->class] = $this->class;

        return true;
    }

    public function load(): bool
    {
        if (!$this->register()) {
            throw ReflectionApiException::forAlreadyDefinedClass($this->class);
        }

        if ($this->isLoaded) {
            return $this->isLoaded;
        }

        try {
            $fileName = tempnam(sys_get_temp_dir(), 'igni-reflection-api');
            $file = fopen($fileName,'w');
            fwrite($file, '<?php ' . $this);
            fclose($file);
            $this->isLoaded = true;

            require_once $fileName;

        } catch (\Throwable $exception) {
            return false;
        }

        return true;
    }

    public function __toString(): string
    {
        return $this->generateCode();
    }
}
