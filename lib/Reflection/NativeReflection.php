<?php

declare(strict_types=1);

namespace AdvancedJsonRpc\Reflection;


use AdvancedJsonRpc\Error;
use AdvancedJsonRpc\ErrorCode;
use AdvancedJsonRpc\Reflection\Dto\Method;
use AdvancedJsonRpc\Reflection\Dto\Parameter;
use AdvancedJsonRpc\Reflection\Dto\Type;
use phpDocumentor\Reflection\DocBlock\Tag;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\Types;
use ReflectionException;
use ReflectionMethod;
use ReflectionNamedType;

class NativeReflection
{
    /** @var DocBlockFactory */
    private $docBlockFactory;
    /** @var \phpDocumentor\Reflection\Types\ContextFactory */
    private $contextFactory;
    /** @var Method[] */
    private $methods = [];

    public function __construct()
    {
        $this->docBlockFactory = DocBlockFactory::createInstance();
        $this->contextFactory = new Types\ContextFactory();
    }

    public function getMethodDetails($rpcMethod, $target, $nativeMethod): Method
    {
        if (array_key_exists($rpcMethod, $this->methods)) {
            return $this->methods[$rpcMethod];
        }

        try {
            $nativeMethod = new ReflectionMethod($target, $nativeMethod);
        } catch (ReflectionException $e) {
            throw new Error($e->getMessage(), ErrorCode::METHOD_NOT_FOUND, null, $e);
        }

        $paramTags = [];
        if ($nativeMethod->getDocComment()) {
            $docBlock = $this->docBlockFactory->create(
                $nativeMethod->getDocComment(),
                $this->contextFactory->createFromReflector($nativeMethod->getDeclaringClass())
            );
            $paramTags = $docBlock->getTagsByName('param');
        }

        $method = new Method(
            $nativeMethod->getDeclaringClass()->getName(),
            $nativeMethod->getDocComment() ?: null,
            array_map(function($p) { return $this->mapNativeReflectionParameterToParameter($p); }, $nativeMethod->getParameters()),
            array_map(function($p) { return $this->mapDocBlockTagToParameter($p); }, $paramTags)
        );

        $this->methods[$rpcMethod] = $method;

        return $method;
    }

    private function mapNativeReflectionParameterToParameter(\ReflectionParameter $native): Parameter
    {
        $types = $this->mapNativeReflectionTypeToType($native->getType());
        return new Parameter($native->getName(), $types);
    }

    private function mapDocBlockTagToParameter(Tag $tag): Parameter
    {
        $type = $tag->getType();
        // For union types, use the first one that is a class array (often it is SomeClass[]|null)
        if ($type instanceof Types\Compound) {
            for ($i = 0; $t = $type->get($i); $i++) {
                if (
                    $t instanceof Types\Array_
                    && $t->getValueType() instanceof Types\Object_
                    && (string)$t->getValueType() !== 'object'
                ) {
                    return new Parameter($tag->getName(), new Type((string)$t->getValueType()->getFqsen()));
                }
            }
        } else if ($type instanceof Types\Array_) {
            return new Parameter($tag->getName(), new Type((string)$type->getValueType()->getFqsen()));
        }
    }

    private function mapNativeReflectionTypeToType(\ReflectionType $native = null): Type
    {
        if ($native instanceof ReflectionNamedType) {
            // We have object data to map and want the class name.
            // This should not include the `?` if the type was nullable.
            return new Type($native->getName());
        } else {
            // Fallback for php 7.0, which is still supported (and doesn't have nullable).
            return new Type((string) $native);
        }
    }
}
