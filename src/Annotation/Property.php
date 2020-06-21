<?php


namespace EasySwoole\HttpAnnotation\Annotation;


class Property
{
    protected $propertyName;
    /** @var \ReflectionProperty */
    protected $reflection;
    protected $annotation = [];

    function __construct(string $name)
    {
        $this->propertyName = $name;
    }

    public function getReflection(): \ReflectionProperty
    {
        return $this->reflection;
    }

    public function setReflection(\ReflectionProperty $reflection): void
    {
        $this->reflection = $reflection;
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