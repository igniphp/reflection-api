<?php declare(strict_types=1);

namespace IgniTest\Fixtures;

class AClass extends TestAbstractClass implements AInterface
{
    private $something = '';

    public function __construct(string $something = 'Relax.')
    {
        $this->something = $something;
    }

    public function doSomething(): string
    {
        return $this->something;
    }
}
