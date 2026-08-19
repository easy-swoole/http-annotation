<?php

namespace EasySwoole\HttpAnnotation;

use EasySwoole\Component\Singleton;
use EasySwoole\Http\ReflectionCache;
use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\ApiGroup;
use EasySwoole\HttpAnnotation\Attributes\ExtendParam;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Attributes\PreCall;
use EasySwoole\HttpAnnotation\Bean\ClassInfo;
use EasySwoole\HttpAnnotation\Bean\ClassMethod;
use EasySwoole\HttpAnnotation\Enum\HttpMethod;
use EasySwoole\HttpAnnotation\Exception\Annotation;
use EasySwoole\HttpAnnotation\Exception\RequestMethodNotAllow;
use ReflectionClass;

class AttributeCache
{
    use Singleton;

    private const forbidMethodList = [
        '__hook', '__destruct',
        '__clone', '__construct', '__call',
        '__callStatic', '__get', '__set',
        '__isset', '__unset', '__sleep',
        '__wakeup', '__toString', '__invoke',
        '__set_state', '__clone', '__debugInfo',
        'onRequest'
    ];


    protected array $map = [];

    function parseClass(string $className)
    {
        if(isset($this->map[$className])){
            return $this->map[$className];
        }
        $classInfo = new ClassInfo();
        $this->map[$className] = $classInfo;

        $reflectionClass = new \ReflectionClass($className);
        $apiGroup = $reflectionClass->getAttributes(ApiGroup::class);
        if(!empty($apiGroup)){
            $apiGroup = $apiGroup[0];
            try {
                $classInfo->apiGroup = $apiGroup->newInstance();
            }catch (\Throwable $throwable){
               throw new Annotation($throwable->getMessage());
            }
        }

        $globalParams = $reflectionClass->getAttributes(Param::class);
        foreach ($globalParams as $param) {
            /** @var Param $param */
            try {
                $param = $param->newInstance();
                $classInfo->globalParams[$param->name] = $param;
            }catch (\Throwable $throwable){
                throw new Annotation($throwable->getMessage());
            }
        }
        //检查是否继承父类参数
        $extendParam = $reflectionClass->getAttributes(ExtendParam::class);
        if(!empty($extendParam)){
            try{
                $classInfo->extendParam = $extendParam[0]->newInstance();
            }catch (\Throwable $throwable){
                throw new Annotation($throwable->getMessage());
            }
        }


        $globalPreCall = $reflectionClass->getAttributes(PreCall::class);
        foreach ($globalPreCall as $preCall) {
            /** @var PreCall $preCall */
            try {
                $preCall = $preCall->newInstance();
                $classInfo->globalPreCall[] = $preCall;
            }catch (\Throwable $throwable){
                throw new Annotation($throwable->getMessage());
            }
        }

        $public = $reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC);
        foreach ($public as $item) {
            if((!in_array($item->getName(),self::forbidMethodList)) && (!$item->isStatic())){
                $api = $item->getAttributes(Api::class);
                if(!empty($api)){
                    try {
                        $apiTag = $api[0]->newInstance();
                        $classInfo->apis[$item->getName()] = $apiTag;
                    }catch (\Throwable $throwable){
                        $msg = "{$throwable->getMessage()} in {$className} method {$item->getName()}";
                        throw new Annotation(message: $msg);
                    }
                }
            }
        }
        return $classInfo;

    }
}