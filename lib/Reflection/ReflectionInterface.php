<?php

declare(strict_types=1);

namespace AdvancedJsonRpc\Reflection;

use AdvancedJsonRpc\Reflection\Dto\Method;

interface ReflectionInterface
{
    public function getMethodDetails($rpcMethod, $target, $nativeMethod): Method;
}
