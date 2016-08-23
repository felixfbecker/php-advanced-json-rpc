<?php
declare(strict_types = 1);

namespace AdvancedJsonRpc\Tests;

use StdClass;
use PHPUnit\Framework\TestCase;
use AdvancedJsonRpc\{Dispatcher, Request};

class DispatcherTest extends TestCase
{
    private $calls;
    private $callsOfNestedTarget;
    private $dispatcher;

    public function setUp()
    {
        $this->calls = [];
        $this->callsOfNestedTarget = [];
        $target = new Target($this->calls);
        $target->nestedTarget = new Target($this->callsOfNestedTarget);
        $this->dispatcher = new Dispatcher($target);
    }

    public function testCallMethodWithoutArgs()
    {
        $result = $this->dispatcher->dispatch((string)new Request(1, 'someMethodWithoutArgs'));
        $this->assertEquals('Hello World', $result);
        $this->assertEquals($this->calls, [new MethodCall('someMethodWithoutArgs', [])]);
    }

    public function testCallMethodWithTypeHintWithPositionalArgs()
    {
        $result = $this->dispatcher->dispatch((string)new Request(1, 'someMethodWithTypeHint', [new Argument('whatever')]));
        $this->assertEquals('Hello World', $result);
        $this->assertEquals($this->calls, [new MethodCall('someMethodWithTypeHint', [new Argument('whatever')])]);
    }

    public function testCallMethodWithTypeHintWithNamedArgs()
    {
        $result = $this->dispatcher->dispatch((string)new Request(1, 'someMethodWithTypeHint', ['arg' => new Argument('whatever')]));
        $this->assertEquals('Hello World', $result);
        $this->assertEquals($this->calls, [new MethodCall('someMethodWithTypeHint', [new Argument('whatever')])]);
    }

    public function testCallMethodWithArrayParamTag()
    {
        $result = $this->dispatcher->dispatch((string)new Request(1, 'someMethodWithArrayParamTag', ['arg' => [new Argument('whatever')]]));
        $this->assertEquals('Hello World', $result);
        $this->assertEquals($this->calls, [new MethodCall('someMethodWithArrayParamTag', [[new Argument('whatever')]])]);
    }

    public function testCallMethodWithUnionTypeParamTag()
    {
        $result = $this->dispatcher->dispatch((string)new Request(1, 'someMethodWithUnionTypeParamTag', ['arg' => [new Argument('whatever')]]));
        $this->assertEquals('Hello World', $result);
        $this->assertEquals($this->calls, [new MethodCall('someMethodWithUnionTypeParamTag', [[new Argument('whatever')]])]);
    }

    public function testCallMethodWithTypeHintWithNamedArgsOnNestedTarget()
    {
        $result = $this->dispatcher->dispatch((string)new Request(1, 'nestedTarget->someMethodWithTypeHint', ['arg' => new Argument('whatever')]));
        $this->assertEquals('Hello World', $result);
        $this->assertEquals($this->calls, []);
        $this->assertEquals($this->callsOfNestedTarget, [new MethodCall('someMethodWithTypeHint', [new Argument('whatever')])]);
    }
}
