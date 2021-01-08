<?php

declare(strict_types=1);

namespace AdvancedJsonRpc\Reflection\Dto;


class Parameter
{
    /** @var string */
    private $name;
    /** @var Type|null */
    private $type;

    public function __construct(string $name, Type $type = null)
    {
        $this->name = $name;
        $this->type = $type;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): Type
    {
        return $this->type;
    }

    public function hasType(): bool
    {
        return isset($this->type);
    }
}
