<?php

namespace EasySwoole\HttpAnnotation;

use EasySwoole\Component\Singleton;
use EasySwoole\HttpAnnotation\Attributes\ApiGroup;

class AttributeCache
{
    use Singleton;

    protected array $apiGroup = [];

    protected array $classReflection = [];

    function addApiGroup(ApiGroup $apiGroup):bool
    {
        if(isset($this->apiGroup[$apiGroup->name])){
            return false;
        }else{
            $this->apiGroup[$apiGroup->name] = $apiGroup;
            return true;
        }
    }

    function apiGroups():array
    {
        return $this->apiGroup;
    }

    function addReflection(\ReflectionClass $reflectionClass):AttributeCache
    {
        $key = md5($reflectionClass->name);
        $this->classReflection[$key] = $reflectionClass;
        return $this;
    }

    function getClassReflections():array
    {
        return $this->classReflection;
    }

    function getClassReflection(string $class):?\ReflectionClass
    {
        $key = md5($class);
        if(isset( $this->classReflection[$key])){
            return $this->classReflection[$key];
        }
        return null;
    }

}