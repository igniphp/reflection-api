<?php declare(strict_types=1);

namespace Igni\Utils\ReflectionApi;

trait StaticTrait
{
    private $static = false;

    public function makeStatic(bool $static = true): self
    {
        $this->static = $static;

        return $this;
    }

    public function isStatic(): bool
    {
        return $this->static;
    }
}
