<?php declare(strict_types=1);

namespace Igni\Utils\ReflectionApi;

class RuntimeArgument implements CodeGenerator
{
    use DefaultValueTrait;

    private $type;
    private $name;
    private $variadic = false;

    public function __construct(string $name, string $type = '')
    {
        $this->name = $name;
        $this->type = $type;
    }

    public function makeVariadic(bool $variadic = true): self
    {
        $this->variadic = $variadic;

        return $this;
    }

    public function isVariadic(): bool
    {
        return $this->variadic;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function generateCode(): string
    {
        $code = '';
        if ($this->type) {
            $code .= $this->type . ' ';
        }
        if ($this->variadic) {
            $code .= '...';
        }
        $code .= "\${$this->name}";

        if ($this->hasDefaultValue) {
            $defaultValue = var_export($this->defaultValue, true);
            if (is_string($this->defaultValue)) {
                $parts = explode('::', $this->defaultValue);
                if ($parts[0] === 'self' || class_exists($parts[0])) {
                    $defaultValue = $this->defaultValue;
                }
            }
            $code .= ' = ' . $defaultValue;
        }

        return $code;
    }

    public function __toString(): string
    {
        return $this->generateCode();
    }
}
