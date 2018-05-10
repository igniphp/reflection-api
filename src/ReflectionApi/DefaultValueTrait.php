<?php declare(strict_types=1);

namespace Igni\Utils\ReflectionApi;

trait DefaultValueTrait
{
    private $defaultValue = null;
    private $hasDefaultValue = false;

    public function setDefaultValue($value): self
    {
        $this->defaultValue = $value;
        $this->hasDefaultValue = true;

        return $this;
    }

    public function hasDefaultValue(): bool
    {
        return $this->hasDefaultValue;
    }

    public function getDefaultValue()
    {
        return $this->defaultValue;
    }
}
