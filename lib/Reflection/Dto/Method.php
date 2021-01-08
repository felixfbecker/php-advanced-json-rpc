<?php

declare(strict_types=1);

namespace AdvancedJsonRpc\Reflection\Dto;

class Method
{
    /** @var string */
    private $declaringClass;
    /** @var string|null */
    private $docComment;
    /** @var Parameter[] */
    private $parameters;
    /** @var Parameter[] */
    private $paramTags;

    /**
     * @param string|null $docComment
     */
    public function __construct(string $declaringClass, $docComment, array $parameters, array $paramTags)
    {
        $this->declaringClass = $declaringClass;
        $this->docComment = $docComment;
        $this->parameters = $parameters;
        $this->paramTags = $paramTags;
    }

    public function getDeclaringClass(): string
    {
        return $this->declaringClass;
    }

    public function getDocComment(): string
    {
        return $this->docComment;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function getParamTags(): array
    {
        return $this->paramTags;
    }
}
