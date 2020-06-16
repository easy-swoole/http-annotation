<?php


namespace EasySwoole\HttpAnnotation\Annotation;


use EasySwoole\Annotation\AbstractAnnotationTag;

class MethodAnnotation
{
    /** @var string */
    protected $methodName;
    /** @var \ReflectionMethod */
    protected $methodReflection;
    protected $annotation = [];

    function __construct(string $methodName)
    {
        $this->methodName = $methodName;
    }

    public function getMethodName(): string
    {
        return $this->methodName;
    }

    public function appendTag(AbstractAnnotationTag $abstractAnnotationTag)
    {
        $this->annotation[$abstractAnnotationTag->tagName()][] = $abstractAnnotationTag;
    }

    public function getMethodReflection(): \ReflectionMethod
    {
        return $this->methodReflection;
    }

    public function setMethodReflection(\ReflectionMethod $methodReflection): void
    {
        $this->methodReflection = $methodReflection;
    }

    public function getAnnotation(): array
    {
        return $this->annotation;
    }

    public function setAnnotation(array $annotation): void
    {
        $this->annotation = $annotation;
    }

    function getAnnotationTag(string $tagName):?array
    {
        if(isset($this->annotation[$tagName])){
            return $this->annotation[$tagName];
        }
        return null;
    }
}