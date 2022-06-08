<?php

namespace EasySwoole\HttpAnnotation;

use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Http\ReflectionCache;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\Param;

abstract class AnnotationController extends Controller
{
    public function __hook(?string $actionName, Request $request, Response $response)
    {
        $this->runAnnotationHook($actionName,$request);
        return parent::__hook($actionName, $request, $response);
    }

    private function runAnnotationHook(string $method,Request $request)
    {

        $actionParams = AttributeCache::getInstance()->getClassMethodMap(static::class,$method);
        if($actionParams === null){
            $actionParams = [];
            $ref = ReflectionCache::getInstance()->getClassReflection(static::class);
            $onRequestParams = $ref->getMethod("onRequest")->getAttributes(Param::class);

            if($ref->hasMethod($method)){
                $actionMethod = $ref->getMethod($method);
                $actionApiTags = $actionMethod->getAttributes(Api::class);
                if(!empty($actionApiTags)){
                    /** @var \ReflectionAttribute $actionApiTag */
                    $actionApiTag = $actionApiTags[0];
                    $actionParams = $actionApiTag->getArguments()['params'];
                }
            }

            foreach ($onRequestParams as $onRequestParam){
                $args = $onRequestParam->getArguments();
                $onRequestParam = new Param(...$args);

                $hit = false;
                foreach ($actionParams as $actionParam){
                    if($actionParam->name == $onRequestParam->name){
                        $hit = true;
                        break;
                    }
                }
                if(!$hit){
                    $actionParams[] = $onRequestParam;
                }
            }

            $allowMethod = ReflectionCache::getInstance()->allowMethodReflections($ref);
            //如果是存在于用户定义的方法，才允许缓存起来。
            if(isset($allowMethod[$method])){
                AttributeCache::getInstance()->setClassMethodMap(static::class,$method,$actionParams);
            }
        }

        /** @var Param $param */
        foreach ($actionParams as $param){
            $param->parsedValue($request);
        }
    }
}