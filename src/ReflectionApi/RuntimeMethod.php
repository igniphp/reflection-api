<?php declare(strict_types=1);

namespace Igni\Utils\ReflectionApi;

use Igni\Utils\Exception\ReflectionApiException;

final class RuntimeMethod implements CodeGenerator
{
    use VisibilityTrait;
    use AbstractTrait;
    use FinalTrait;
    use StaticTrait;

    private $returnType = '';
    private $arguments = [];
    private $body = [];
    private $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setReturnType(string $type): self
    {
        $this->returnType = $type;

        return $this;
    }

    public function getReturnType(): string
    {
        return $this->returnType;
    }

    public function addArgument(RuntimeArgument $argument): self
    {
        $this->arguments[$argument->getName()] = $argument;

        return $this;
    }

    public function setBody(string ...$lines): self
    {
        if ($this->abstract) {
            throw ReflectionApiException::forAbstractMethodWithBody($this->name);
        }

        $this->body = $lines;

        return $this;
    }

    public function addLine(string $line): self
    {
        if ($this->abstract) {
            throw ReflectionApiException::forAbstractMethodWithBody($this->name);
        }

        $this->body[] = $line;

        return $this;
    }

    public function generateCode(): string
    {
        $code = '';

        if ($this->isAbstract()) {
            $code .= 'abstract';
        }

        if ($this->isFinal()) {
            $code .= 'final ';
        }

        $code .= $this->getVisibility() . ' ';

        if ($this->isStatic()) {
            $code .= 'static ';
        }
        $code .= "function {$this->name}(";

        if ($this->arguments) {
            $arguments = [];
            foreach ($this->arguments as $argument) {
                $arguments[] = $argument->generateCode();
            }

            $code .= implode(', ', $arguments);
        }

        $code .= ')';
        if ($this->returnType) {
            $code .= ": {$this->returnType}";
        }

        if ($this->isAbstract()) {
            $code .= ';';
        } else {
            $code .= "\n{";

            if ($this->body) {
                $code .= "\n\t" . implode("\n\t", $this->body);
            }

            $code .= "\n}";
        }

        return $code;
    }
}
