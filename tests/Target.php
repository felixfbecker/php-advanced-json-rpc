<?php
declare(strict_types = 1);

namespace AdvancedJsonRpc\Tests;

class Target
{
    public $nestedTarget;

    private $calls = [];

    public function __construct(array &$calls)
    {
        $this->calls = &$calls;
    }

    public function someMethodWithoutArgs()
    {
        $this->calls[] = new MethodCall('someMethodWithoutArgs', func_get_args());
        return 'Hello World';
    }

    public function someMethodWithTypeHint(Argument $arg)
    {
        $this->calls[] = new MethodCall('someMethodWithTypeHint', func_get_args());
        return 'Hello World';
    }

    /**
     * @param Argument[] $arg
     */
    public function someMethodWithArrayParamTag($arg)
    {
        $this->calls[] = new MethodCall('someMethodWithArrayParamTag', func_get_args());
        return 'Hello World';
    }

    /**
     * @param null|Argument[] $arg
     */
    public function someMethodWithUnionTypeParamTag($arg)
    {
        $this->calls[] = new MethodCall('someMethodWithUnionTypeParamTag', func_get_args());
        return 'Hello World';
    }

    public function someMethodWithDifferentlyTypedArgs(string $arg1 = null, int $arg2 = null)
    {
        $this->calls[] = new MethodCall('someMethodWithDifferentlyTypedArgs', func_get_args());
        return 'Hello World';
    }

    /**
     * @param Argument[] $args
     */
    public function someMethodWithArrayTypeHint(array $args): string
    {
        $this->calls[] = new MethodCall('someMethodWithArrayTypeHint', func_get_args());
        return 'Hello World';
    }
}
