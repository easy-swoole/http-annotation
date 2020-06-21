<?php


namespace EasySwoole\HttpAnnotation\Annotation;


use EasySwoole\Annotation\AbstractAnnotationTag;

class Method
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

    public function getAnnotations(): array
    {
        return $this->annotation;
    }

    public function setAnnotation(array $annotation): void
    {
        $this->annotation = $annotation;
    }

    function getAnnotationTag(string $tagName,$index = false)
    {
        if(isset($this->annotation[$tagName])){
            if($index !== false){
                return $this->annotation[$tagName][0];
            }
            return $this->annotation[$tagName];
        }
        return null;
    }
}