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

    function setClassMethodMap(string $class,$action,array $data):void
    {
        $key = md5($class);
        $this->classMethodMap[$key][$action] = $data;
    }

    function getClassMethodMap(string $class,$action):?array
    {
        $key = md5($class);
        if(isset($this->classMethodMap[$key][$action])){
            $list = [];
            foreach ($this->classMethodMap[$key][$action] as $item){
                $list[] = clone $item;
            }
            return $list;
        }
        return null;
    }
}