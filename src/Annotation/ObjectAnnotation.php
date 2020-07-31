<?php


namespace EasySwoole\HttpAnnotation\Annotation;


use EasySwoole\Annotation\Annotation;
use EasySwoole\HttpAnnotation\AnnotationTag\ApiGroup;
use EasySwoole\HttpAnnotation\AnnotationTag\ApiGroupAuth;
use EasySwoole\HttpAnnotation\AnnotationTag\ApiGroupDescription;

class ObjectAnnotation
{
    /** @var ApiGroup|null */
    protected $apiGroup;
    /** @var ApiGroupDescription|null */
    protected $apiGroupDescription;

    protected $groupAuth = [];

    protected $methods = [];

    protected $properties = [];

    private $annotation;

    function __construct(Annotation $annotation)
    {
        $this->annotation = $annotation;
    }

    function parse(\ReflectionClass $reflection)
    {

    }

    function addGroupAuth(ApiGroupAuth $apiGroupAuth):ObjectAnnotation
    {
        $this->groupAuth[$apiGroupAuth->name] = $apiGroupAuth;
        return $this;
    }

    function getGroupAuth(?string $paramName = null)
    {
        if($paramName && isset($this->groupAuth[$paramName])){
            return $this->groupAuth[$paramName];
        }else{
            return $this->groupAuth;
        }
    }

    function addMethod(MethodAnnotation $method)
    {
        $this->methods[$method->getName()] = $method;
        return $this;
    }

    function addProperty(PropertyAnnotation $annotation)
    {
        $this->properties[$annotation->getName()] = $annotation;
        return $this;
    }

    function getProperty(?string $name = null)
    {
        if($name && isset($this->properties[$name])){
            return $this->properties[$name];
        }else{
            return $this->properties;
        }
    }

    function getMethod(?string $name = null)
    {
        if($name && isset($this->methods[$name])){
            return $this->methods[$name];
        }else{
            return $this->methods;
        }
    }

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
}