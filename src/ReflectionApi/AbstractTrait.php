<?php declare(strict_types=1);

namespace Igni\Utils\ReflectionApi;

use Igni\Utils\Exception\ReflectionApiException;

trait AbstractTrait
{
    private $abstract = false;

    public function makeAbstract(bool $abstract = true): self
    {
        if (isset($this->final) && $this->final) {
            throw ReflectionApiException::forFinalAbstract();
        }

        $this->abstract = $abstract;

        return $this;
    }

    public function isAbstract(): bool
    {
        return $this->abstract;
    }
}
