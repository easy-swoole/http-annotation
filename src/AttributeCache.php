<?php

namespace EasySwoole\HttpAnnotation;

use EasySwoole\Component\Singleton;
use EasySwoole\Http\ReflectionCache;
use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\ApiGroup;
use EasySwoole\HttpAnnotation\Attributes\ExtendParam;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Attributes\PreCall;
use EasySwoole\HttpAnnotation\Bean\ClassAttribute;
use EasySwoole\HttpAnnotation\Bean\ClassOnRequest;
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

    function parseClass(string $className):ClassAttribute
    {
        if(isset($this->map[$className])){
            return $this->map[$className];
        }
        $classInfo = new ClassAttribute();
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
        foreach ($public as $methodItem) {
            if((!in_array($methodItem->getName(),self::forbidMethodList)) && (!$methodItem->isStatic())){
                $api = $methodItem->getAttributes(Api::class);
                if(!empty($api)){
                    try {
                        $apiTag = $api[0]->newInstance();
                        $classInfo->apis[$methodItem->getName()] = $apiTag;
                    }catch (\Throwable $throwable){
                        $msg = "{$throwable->getMessage()} in {$className} method {$methodItem->getName()}";
                        throw new Annotation(message: $msg);
                    }
                }
                $preCalls = $methodItem->getAttributes(PreCall::class);
                foreach ($preCalls as $preCall) {
                    try{
                        $preCall = $preCall->newInstance();
                        $classInfo->methodPreCall[$methodItem->getName()][] = $preCall;
                    }catch (\Throwable $throwable){
                        throw new Annotation(message: $throwable->getMessage());
                    }
                }
            }
        }

        $onRequest = $reflectionClass->getMethod('onRequest');
        //检查是否继承父类参数
        $extendParam = $onRequest->getAttributes(ExtendParam::class);
        if(!empty($extendParam)){
            try{
                $classInfo->onRequest->extendParam = $extendParam[0]->newInstance();
            }catch (\Throwable $throwable){
                throw new Annotation($throwable->getMessage());
            }
        }
        //检查参数定义
        $params = $onRequest->getAttributes(Param::class);
        foreach ($params as $param) {
            try{
                $param = $param->newInstance();
                $classInfo->onRequest->onRequestParams[$param->name] = $param;
            }catch (\Throwable $throwable){
                $msg = "{$throwable->getMessage()} in {$className} method onRequest";
                throw new Annotation(message: $msg);
            }
        }
        if($classInfo->onRequest->extendParam){
            $parentClass = $reflectionClass->getParentClass();
            if($parentClass->isSubclassOf(AnnotationController::class)){
                $parentAttribute = static::getInstance()->parseClass($parentClass->getName());
                $parentOnRequestParams = $parentAttribute->onRequest->onRequestParams;
                if(!empty($classInfo->onRequest->extendParam->parentParamsName)){
                    /** @var Param $temp */
                    foreach ($parentOnRequestParams as $temp){
                        if(!in_array($temp->name,$classInfo->onRequest->extendParam->parentParamsName)){
                            unset($parentOnRequestParams[$temp->name]);
                        }
                    }
                }
                foreach ($parentOnRequestParams as $temp){
                    //子类定义优先
                    if(!isset($classInfo->onRequest->onRequestParams[$temp->name])){
                        $classInfo->onRequest->onRequestParams[$temp->name] = $temp;
                    }
                }
            }
        }

        return $classInfo;

    }
}