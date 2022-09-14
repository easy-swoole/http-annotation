<?php

namespace EasySwoole\HttpAnnotation;

use EasySwoole\Component\Singleton;
use EasySwoole\HttpAnnotation\Attributes\ApiGroup;

class AttributeCache
{
    use Singleton;

    protected array $apiGroup = [];
    protected array $classMethodMap = [];

    function addApiGroup(ApiGroup $apiGroup):bool
    {
        if(isset($this->apiGroup[$apiGroup->groupName])){
            return false;
        }else{
            $this->apiGroup[$apiGroup->groupName] = $apiGroup;
            return true;
        }
    }

    function apiGroups():array
    {
        return $this->apiGroup;
    }

    function setClassMethodMap(string $class,$action,array $data):void
    {
        $key = md5($class);
        $this->classMethodMap[$key][$action] = $data;
    }

    /*
     * 注意引用克隆
     */
    function getClassMethodMap(string $class,$action):?array
    {
        $key = md5($class);
        if(isset($this->classMethodMap[$key][$action])){
            return $this->classMethodMap[$key][$action];
        }
        return null;
    }
}