<?php declare(strict_types=1);

namespace Igni\Utils\ReflectionApi;

class RuntimeProperty implements CodeGenerator
{
    use VisibilityTrait;
    use DefaultValueTrait;
    use StaticTrait;

    private $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function generateCode(): string
    {
        $code = $this->visibility;

        if ($this->isStatic()) {
            $code .= ' static';
        }

        $code .= ' $' . $this->name;

        if ($this->hasDefaultValue()) {
            $code .= ' = ' . var_export($this->defaultValue, true);
        }

        return $code . ';';
    }
}
