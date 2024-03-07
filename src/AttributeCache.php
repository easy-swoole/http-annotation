<?php

namespace EasySwoole\HttpAnnotation;

use EasySwoole\Component\Singleton;
use EasySwoole\Http\ReflectionCache;
use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\ApiGroup;
use EasySwoole\HttpAnnotation\Attributes\PreCall;
use EasySwoole\HttpAnnotation\Enum\HttpMethod;
use EasySwoole\HttpAnnotation\Exception\Annotation;
use EasySwoole\HttpAnnotation\Exception\RequestMethodNotAllow;

class AttributeCache
{
    use Singleton;

    /** @var array
     *包括了method、onRequest和控制器全局
     */
    protected array $classActionParams = [];

    protected array $classMethodParams = [];

    protected array $classMethodApiTags = [];

    protected array $classMethodPreCallTags = [];

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

    function getClassMethodApiTag(string $class,string $action)
    {
        $key = md5($class);
        if(isset($this->classMethodApiTags[$key][$action])){
            if($this->classMethodApiTags[$key][$action] instanceof Api){
                return $this->classMethodApiTags[$key][$action];
            }
            return  null;
        }
        $class = ReflectionCache::getInstance()->getClassReflection($class);
        $ref = ReflectionCache::getInstance()->allowMethodReflections($class);
        if(!isset($ref[$action])){
            return null;
        }
        /** @var \ReflectionMethod $ref */
        $ref = $ref[$action];
        $actionApiTags = $ref->getAttributes(Api::class);
        if(!empty($actionApiTags)){
            try{
                $apiTag = new Api(...$actionApiTags[0]->getArguments());
            }catch (\Throwable $exception){
                $class = static::class;
                $msg = "{$exception->getMessage()} in controller: {$class} method: {$action}";
                throw new Annotation($msg);
            }

            $this->classMethodApiTags[$key][$action] = $apiTag;
            return $apiTag;
        }else{
            $this->classMethodApiTags[$key][$action] = true;
        }
        return null;
    }

    function getClassMethodPreCallTag(string $class,string $action):?array
    {
        $key = md5($class);
        if(isset($this->classMethodPreCallTags[$key][$action])){
            if(is_array($this->classMethodPreCallTags[$key][$action])){
                return $this->classMethodPreCallTags[$key][$action];
            }
            return  null;
        }
        $class = ReflectionCache::getInstance()->getClassReflection($class);
        $ref = ReflectionCache::getInstance()->allowMethodReflections($class);
        if(!isset($ref[$action])){
            return null;
        }
        /** @var \ReflectionMethod $ref */
        $ref = $ref[$action];
        $actionPreCallTags = $ref->getAttributes(PreCall::class);
        if(!empty($actionPreCallTags)){
            $final = [];
            foreach ($actionPreCallTags as $callTag){
                try{
                    $callTag = new Api(...$callTag->getArguments());
                    $final[] = $callTag;
                }catch (\Throwable $exception){
                    $class = static::class;
                    $msg = "{$exception->getMessage()} in controller: {$class} method: {$action}";
                    throw new Annotation($msg);
                }
            }
            $this->classMethodPreCallTags[$key][$action] = $final;
            return $final;
        }else{
            $this->classMethodPreCallTags[$key][$action] = true;
        }
        return null;
    }
}