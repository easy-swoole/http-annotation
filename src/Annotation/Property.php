<?php


namespace EasySwoole\HttpAnnotation\Annotation;


class Property
{
    protected $propertyName;
    /** @var \ReflectionProperty */
    protected $methodReflection;
    protected $annotation = [];

    function __construct(string $name)
    {
        $this->propertyName = $name;
    }

    public function getMethodReflection(): \ReflectionProperty
    {
        return $this->methodReflection;
    }

    public function setMethodReflection(\ReflectionProperty $methodReflection): void
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