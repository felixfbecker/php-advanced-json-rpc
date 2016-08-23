<?php
declare(strict_types = 1);

namespace AdvancedJsonRpc\Tests;

class MethodCall
{
    public $method;
    public $args;

    public function __construct(string $method, array $args)
    {
        $this->method = $method;
        $this->args = $args;
    }
}
