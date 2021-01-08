<?php

declare(strict_types=1);

namespace AdvancedJsonRpc\Reflection\Dto;

class Type
{
    /** @var string */
    private $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
