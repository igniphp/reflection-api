<?php declare(strict_types=1);

namespace Igni\Utils\ReflectionApi;

interface CodeGenerator
{
    public function generateCode(): string;
}
