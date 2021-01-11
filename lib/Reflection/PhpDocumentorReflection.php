<?php

declare(strict_types=1);

namespace AdvancedJsonRpc\Reflection;


use AdvancedJsonRpc\Reflection\Dto\Method;
use AdvancedJsonRpc\Reflection\Dto\Parameter;
use AdvancedJsonRpc\Reflection\Dto\Type;
use phpDocumentor\Reflection\DocBlock\Tag;
use phpDocumentor\Reflection\File\LocalFile;
use phpDocumentor\Reflection\Php\Argument;
use phpDocumentor\Reflection\Php\Project;
use phpDocumentor\Reflection\Php\ProjectFactory;
use phpDocumentor\Reflection\Types;
use ReflectionMethod;

class PhpDocumentorReflection implements ReflectionInterface
{

    /** @var ProjectFactory */
    private $projectFactory;

    public function __construct()
    {
        $this->projectFactory = ProjectFactory::createInstance();
    }

    public function getMethodDetails($rpcMethod, $target, $nativeMethodName): Method
    {
        $nativeMethod = new ReflectionMethod($target, $nativeMethodName);
        $projectFiles = [new LocalFile($nativeMethod->getFileName())];
        /** @var Project $project */
        $project = $this->projectFactory->create('php-advanced-json-rpc', $projectFiles);

        /** @var \phpDocumentor\Reflection\Php\Class_ $class */
        $class = $project->getFiles()[$nativeMethod->getFileName()]->getClasses()['\\' . $nativeMethod->class]; /* @todo add error handling of multiple classes in single file */
        $methodName = '\\' . $nativeMethod->class . '::' . $nativeMethod->getName() . '()';
        $method = $class->getMethods()[$methodName]; /* @todo add error handling for missing method */

        $parameters = array_map(function ($a) { return $this->mapPhpDocumentorReflectionParameterToParameter($a); }, $method->getArguments());

        /* Improve types from the doc block */
        $docBlock = $method->getDocBlock();
        if ($docBlock !== null) {
            $docBlockParameters = [];

            foreach ($method->getDocBlock()->getTagsByName('param') as $param) {
                $docBlockParameters[$param->getVariableName()] = $this->mapDocBlockTagToParameter($param);
            }

            foreach ($parameters as $position => $param) {
                if (array_key_exists($param->getName(), $docBlockParameters) && $docBlockParameters[$param->getName()]->hasType())
                {
                    $parameters[$position] = $docBlockParameters[$param->getName()];
                }
            }
        }

        return new Method($parameters);
    }

    private function mapPhpDocumentorReflectionParameterToParameter(Argument $argument): Parameter
    {
        $phpDocumentorType = $argument->getType();
        if ($phpDocumentorType === null) {
            return new Parameter($argument->getName());
        }

        return new Parameter($argument->getName(), new Type((string) $phpDocumentorType));
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
                    return new Parameter($tag->getVariableName(), new Type((string)$t->getValueType()->getFqsen()));
                }
            }
        } else if ($type instanceof Types\Array_) {
            return new Parameter($tag->getVariableName(), new Type((string)$type->getValueType()->getFqsen()));
        }
    }
}
