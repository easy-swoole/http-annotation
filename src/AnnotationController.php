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
        $this->runAnnotationHook($actionName);
        return parent::__hook($actionName, $request, $response);
    }

    private function runAnnotationHook(string $method)
    {
        $ref = ReflectionCache::getInstance()->getClassReflection(static::class);
        $onRequestParams = $ref->getMethod("onRequest")->getAttributes(Param::class);


        $actionParams = [];
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
            $hit = false;
            foreach ($actionParams as $actionParam){
                if($actionParam->name == $onRequestParam->getArguments()['name']){
                    $hit = true;
                    break;
                }
            }
            if(!$hit){
                $actionParams[] = $onRequestParam->getArguments();
            }
        }
    }
}