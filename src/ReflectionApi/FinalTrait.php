<?php declare(strict_types=1);

namespace Igni\Utils\ReflectionApi;

use Igni\Utils\Exception\ReflectionApiException;

trait FinalTrait
{
    private $final = false;

    public function makeFinal(bool $final = true): self
    {
        if (isset($this->abstract) && $this->abstract) {
            throw ReflectionApiException::forFinalAbstract();
        }

        $this->final = $final;

        return $this;
    }

    public function isFinal(): bool
    {
        return $this->final;
    }
}
