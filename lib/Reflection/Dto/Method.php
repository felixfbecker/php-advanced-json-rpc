<?php

declare(strict_types=1);

namespace AdvancedJsonRpc\Reflection\Dto;

class Method
{
    /** @var Parameter[] */
    private $parameters;

    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function hasParameter(int $name): bool
    {
        return array_key_exists($name, $this->parameters);
    }

    public function getParameter(int $name): Parameter
    {
        return $this->parameters[$name];
    }
}
