<?php

namespace EasySwoole\HttpAnnotation;

use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\ExtendParam;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Exception\Annotation;
use FastRoute\RouteCollector;

class Utility
{
    public static function parseMethodParams(\ReflectionClass $reflectionClass,string $methodName):array
    {
        if(!$reflectionClass->hasMethod($methodName)){
            return [];
        }
        $actionMethodRef = $reflectionClass->getMethod($methodName);
        $actionApiTags = $actionMethodRef->getAttributes(Api::class);
        $finalParams = [];
        if(!empty($actionApiTags)){
            try{
                $apiTag = new Api(...$actionApiTags[0]->getArguments());
                /** @var Param $item */
                foreach ($apiTag->requestParam as $item){
                    $finalParams[$item->name] = $item;
                }
            }catch (\Throwable $exception){
                $msg = "{$exception->getMessage()} in controller: {$reflectionClass->name} method: {$methodName}";
                throw new Annotation($msg);
            }
        }else{
            $actionParamTags = $actionMethodRef->getAttributes(Param::class);
            foreach ($actionParamTags as $actionParamTag){
                try{
                    $param = new Param(...$actionParamTag->getArguments());
                    if(!isset($finalParams[$param->name])){
                        $finalParams[$param->name] = $param;
                    }else{
                        throw new Annotation("can not redefine param {$param->name}");
                    }
                }catch (\Throwable $exception){
                    $msg = "{$exception->getMessage()} in controller: {$reflectionClass->name} method: {$methodName}";
                    throw new Annotation($msg);
                }
            }
        }


        //检查是否继承父类
        $extendParents = [];
        $extendParent = $actionMethodRef->getAttributes(ExtendParam::class);
        if(!empty($extendParent)){
            $extendParent = new ExtendParam(...$extendParent[0]->getArguments());
            $parentClass = $reflectionClass->getParentClass();
            if($parentClass){
                $parentParams = self::parseMethodParams(new \ReflectionClass($parentClass->name),$methodName);
                if(!empty($extendParent->parentParams)){
                    $temp = [];
                    foreach ($extendParent->parentParams as $paramName){
                        if(isset($parentParams[$paramName])){
                            $temp[$paramName] = $parentParams[$paramName];
                        }else{
                            $msg = "param {$paramName} is not define in parent method {$methodName} of class {$reflectionClass->name}";
                            throw new Annotation($msg);
                        }
                    }
                    $parentParams = $temp;
                }
                foreach ($parentParams as $paramName => $param){
                    if(!isset($finalParams[$paramName])){
                        $finalParams[$paramName] = $param;
                    }
                }
            }
        }


        return $finalParams;
    }

    static function mappingRouter(
        RouteCollector $routeCollector,
        string $controllerPath,
        string $controllerNameSpace = 'App\HttpController'
    ):void{

    }
}