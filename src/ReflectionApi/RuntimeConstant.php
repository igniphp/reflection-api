<?php declare(strict_types=1);

namespace Igni\Utils\ReflectionApi;

class RuntimeConstant implements CodeGenerator
{
    use VisibilityTrait;

    private $name;
    private $value;

    public function __construct(string $name, $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function generateCode(): string
    {
        return "{$this->visibility} const {$this->name} = " . var_export($this->value, true) . ';';
    }
}
