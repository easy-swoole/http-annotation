<?php

namespace EasySwoole\HttpAnnotation;

use EasySwoole\Component\Singleton;
use EasySwoole\HttpAnnotation\Attributes\ApiGroup;

class AttributeCache
{
    use Singleton;

    protected array $classMethodFullParams = [];

    protected array $classMethodParams = [];

    function setClassMethodFullParams(string $class, $action, array $data):void
    {
        $key = md5($class);
        $this->classMethodFullParams[$key][$action] = $data;
    }

    /*
     * 注意引用克隆
     */
    function getClassMethodFullParams(string $class,string $action):?array
    {
        $key = md5($class);
        if(isset($this->classMethodFullParams[$key][$action])){
            return $this->classMethodFullParams[$key][$action];
        }
        return null;
    }

    function setClassMethodParams(string $class,string $action, array $data):void
    {
        $key = md5($class);
        $this->classMethodParams[$key][$action] = $data;
    }

    function getClassMethodParams(string $class,string $action)
    {
        $key = md5($class);
        if(isset($this->classMethodParams[$key][$action])){
            return $this->classMethodParams[$key][$action];
        }
        return null;
    }
}