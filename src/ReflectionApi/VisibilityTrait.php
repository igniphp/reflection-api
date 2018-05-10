<?php declare(strict_types=1);

namespace Igni\Utils\ReflectionApi;

trait VisibilityTrait
{
    private $visibility = 'public';

    public function makePrivate(): self
    {
        $this->visibility = 'private';

        return $this;
    }

    public function makePublic(): self
    {
        $this->visibility = 'public';

        return $this;
    }

    public function makeProtected(): self
    {
        $this->visibility = 'protected';

        return $this;
    }

    public function isPrivate(): bool
    {
        return $this->visibility === 'private';
    }

    public function isProtected(): bool
    {
        return $this->visibility === 'protected';
    }

    public function isPublic(): bool
    {
        return $this->visibility === 'public';
    }

    public function getVisibility(): string
    {
        return $this->visibility;
    }
}
