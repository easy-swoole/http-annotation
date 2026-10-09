<?php

namespace EasySwoole\HttpAnnotation;

use EasySwoole\Component\Singleton;
use EasySwoole\Http\ReflectionCache;
use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\ApiGroup;
use EasySwoole\HttpAnnotation\Attributes\ExtendParam;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Attributes\PreCall;
use EasySwoole\HttpAnnotation\Attributes\Property\Context;
use EasySwoole\HttpAnnotation\Attributes\Property\Di;
use EasySwoole\HttpAnnotation\Bean\ClassAttribute;
use EasySwoole\HttpAnnotation\Bean\ClassOnRequest;
use EasySwoole\HttpAnnotation\Bean\PropertyAttribute;
use EasySwoole\HttpAnnotation\Enum\HttpMethod;
use EasySwoole\HttpAnnotation\Enum\ParamFrom;
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

        $reflectionClass = new \ReflectionClass($className);
        $apiGroup = $reflectionClass->getAttributes(ApiGroup::class);
        if(!empty($apiGroup)){
            $apiGroup = $apiGroup[0];
            try {
                /** @var ApiGroup $t */
                $t = $apiGroup->newInstance();
                $t->relateClass = $reflectionClass->getName();
                if($t->description){
                    $t->description->relateClass = $reflectionClass->getName();
                }
                $classInfo->apiGroup = $t;
            }catch (\Throwable $throwable){
               throw new Annotation("{$throwable->getMessage()} in {$className} for ApiGroup attribute");
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
                        /** @var Api $apiTag */
                        $apiTag = $api[0]->newInstance();
                        $apiTag->relateClass = $reflectionClass->getName();
                        $apiTag->relateMethod = $methodItem->getName();
                        if($apiTag->description){
                            $apiTag->description->relateClass = $reflectionClass->getName();
                            $apiTag->description->relateMethod = $methodItem->getName();
                        }
                        $apiTag->apiName = $methodItem->getName();
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

        $properties = $reflectionClass->getProperties(\ReflectionMethod::IS_PUBLIC|\ReflectionMethod::IS_PROTECTED);
        foreach ($properties as $propertyItem) {
            $attr = new PropertyAttribute();
            $di = $propertyItem->getAttributes(Di::class);
            if(!empty($di)){
                try {
                    /** @var Di $di */
                    $di = $di[0]->newInstance();
                    $attr->di = $di;
                }catch (\Throwable $throwable){
                    throw new Annotation("{$throwable->getMessage()} in {$className} property {$propertyItem->getName()} for Di attribute");
                }
            }
            $context = $propertyItem->getAttributes(Context::class);
            if(!empty($context)){
                if($di){
                    throw new Annotation("class {$className} property {$propertyItem->getName()} Di Attribute is already defined");
                }
                try {
                    /** @var Context $context */
                    $context = $context[0]->newInstance();
                    $attr->context = $context;
                }catch (\Throwable $throwable){
                    throw new Annotation("{$throwable->getMessage()} in {$className} property {$propertyItem->getName()} for Context attribute");
                }
            }

            $classInfo->propertyAttribute[$propertyItem->getName()] = $attr;
        }

        $onRequest = $reflectionClass->getMethod('onRequest');
        //检查是否继承父类参数
        $extendParam = $onRequest->getAttributes(ExtendParam::class);
        if(!empty($extendParam)){
            try{
                /** @var  ExtendParam $t */
                $t = $extendParam[0]->newInstance();
                $classInfo->onRequest->extendParam = $t;
            }catch (\Throwable $throwable){
                $msg = "{$throwable->getMessage()} for ExtendParam attribute in {$className} method onRequest";
                throw new Annotation(message: $msg);
            }
        }
        //检查参数定义
        $params = $onRequest->getAttributes(Param::class);
        foreach ($params as $param) {
            try{
                $param = $param->newInstance();
                $classInfo->onRequest->onRequestParams[$param->name] = $param;
            }catch (\Throwable $throwable){
                $msg = "{$throwable->getMessage()} for Param attribute in {$className} method onRequest";
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

        // 继承与覆盖完成后，检查实际作用于 GET action 的公共参数。
        foreach ($classInfo->apis as $actionName => $api) {
            if ($api->allowMethod !== HttpMethod::GET) {
                continue;
            }
            foreach ($classInfo->onRequest->onRequestParams as $param) {
                if (in_array($actionName, $param->ignoreAction, true)
                    || isset($api->requestParam[$param->name])) {
                    continue;
                }
                if (!in_array(ParamFrom::GET, $param->from, true)) {
                    $sources = implode(', ', array_map(static fn(ParamFrom $source): string => $source->name, $param->from));
                    throw new Annotation(
                        "class {$className}, action {$actionName}: Api allowMethod GET conflicts with onRequest param {$param->name} FROM [{$sources}]; "
                        . 'the applicable onRequest parameter must include ParamFrom::GET, be overridden by the action, or exclude this action via ignoreAction'
                    );
                }
            }
        }

        // 只缓存完整且通过校验的扫描结果。
        $this->map[$className] = $classInfo;
        return $classInfo;

    }
}