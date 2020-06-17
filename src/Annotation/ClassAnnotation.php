<?php


namespace EasySwoole\HttpAnnotation\Annotation;


use EasySwoole\HttpAnnotation\AnnotationTag\ApiGroup;
use EasySwoole\HttpAnnotation\AnnotationTag\ApiGroupDescription;

class ClassAnnotation
{
    /**
     * @var ApiGroup|null
     */
    protected $apiGroup;
    /**
     * @var ApiGroupDescription|null
     */
    protected $apiGroupDescription;

    protected $apiGroupAuth = [];

    protected $methods = [];

    /**
     * @return ApiGroup|null
     */
    public function getApiGroup(): ?ApiGroup
    {
        return $this->apiGroup;
    }

    /**
     * @param ApiGroup|null $apiGroup
     */
    public function setApiGroup(?ApiGroup $apiGroup): void
    {
        $this->apiGroup = $apiGroup;
    }

    /**
     * @return ApiGroupDescription|null
     */
    public function getApiGroupDescription(): ?ApiGroupDescription
    {
        return $this->apiGroupDescription;
    }

    /**
     * @param ApiGroupDescription|null $apiGroupDescription
     */
    public function setApiGroupDescription(?ApiGroupDescription $apiGroupDescription): void
    {
        $this->apiGroupDescription = $apiGroupDescription;
    }

    public function getApiGroupAuth():array
    {
        return $this->apiGroupAuth;
    }

    public function setApiGroupAuth(array $array): void
    {
        $this->apiGroupAuth = $array;
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

    function getMethod(string $name):?MethodAnnotation
    {
        if(isset($this->methods[$name])){
            return $this->methods[$name];
        }
        return null;
    }

    function addMethod(string $name):MethodAnnotation
    {
        $instance = new MethodAnnotation($name);
        $this->methods[$name] = $instance;
        return $instance;
    }

}