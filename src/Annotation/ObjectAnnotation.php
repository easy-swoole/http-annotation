<?php


namespace EasySwoole\HttpAnnotation\Annotation;


class ObjectAnnotation extends ApiGroup
{
    protected $methods = [];
    protected $properties = [];
    /** @var \ReflectionClass */
    protected $reflection;

    /**
     * @return \ReflectionClass
     */
    public function getReflection(): \ReflectionClass
    {
        return $this->reflection;
    }

    /**
     * @param \ReflectionClass $reflection
     */
    public function setReflection(\ReflectionClass $reflection): void
    {
        $this->reflection = $reflection;
    }

    /**
     * @return array
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * @param array $methods
     */
    public function setMethods(array $methods): void
    {
        $this->methods = $methods;
    }

    function getMethod(string $name):?Method
    {
        if(isset($this->methods[$name])){
            return $this->methods[$name];
        }
        return null;
    }

    function addMethod(string $name):Method
    {
        $instance = new Method($name);
        $this->methods[$name] = $instance;
        return $instance;
    }

    /**
     * @return array
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * @param array $properties
     */
    public function setProperties(array $properties): void
    {
        $this->properties = $properties;
    }

    public function addProperty(string $name)
    {
        $instance = new Property($name);
        $this->properties[$name] = $instance;
        return $instance;
    }

    public function getProperty(string $name):?Property
    {
        if(isset($this->properties[$name])){
            return $this->properties[$name];
        }
        return null;
    }
}