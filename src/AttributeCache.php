<?php

namespace EasySwoole\HttpAnnotation;

use EasySwoole\Component\Singleton;
use EasySwoole\HttpAnnotation\Attributes\ApiGroup;

class AttributeCache
{
    use Singleton;

    /** @var array
     *包括了method、onRequest和控制器全局
     */
    protected array $classActionParams = [];

    protected array $classMethodParams = [];

    function setClassActionParams(string $class, $action, array $data):void
    {
        $key = md5($class);
        $this->classActionParams[$key][$action] = $data;
    }

    /*
     * 注意引用克隆
     */
    function getClassActionParams(string $class, string $action):?array
    {
        $key = md5($class);
        if(isset($this->classActionParams[$key][$action])){
            return $this->classActionParams[$key][$action];
        }
        return null;
    }

    function setClassMethodParams(string $class,string $action, array $data):void
    {
        $key = md5($class);
        $this->classMethodParams[$key][$action] = $data;
    }

    function getClassMethodParams(string $class,string $action):?array
    {
        $key = md5($class);
        if(isset($this->classMethodParams[$key][$action])){
            return $this->classMethodParams[$key][$action];
        }
        return null;
    }
}