<?php

namespace EasySwoole\HttpAnnotation;

use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Http\ReflectionCache;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Attributes\Validator\AbstractValidator;
use EasySwoole\HttpAnnotation\Exception\ParamError;
use EasySwoole\HttpAnnotation\Exception\ValidateFail;

abstract class AnnotationController extends Controller
{
    public function __hook(?string $actionName, Request $request, Response $response,array $actionArg = [])
    {
        try{
            $ret = $this->runAnnotationHook($actionName,$request);
            $ref = ReflectionCache::getInstance()->getClassReflection(static::class)->getMethod($actionName);
            foreach ($ref->getParameters() as $parameter){
                $key = $parameter->name;
                if(isset($ret[$key])){
                    $actionArg[$key] = $ret[$key]->parsedValue();
                }else{
                    throw new ParamError("method {$actionName}() require arg: {$key} , but not define in any controller annotation");
                }
            }
        }catch (\Throwable $exception){
            $this->onException($exception);
            return ;
        }

        parent::__hook($actionName, $request, $response,$actionArg);
    }

    private function runAnnotationHook(string $method,Request $request):array
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

        $finalParams = [];
        /** @var Param $param */
        foreach ($actionParams as $param){
            $param->parsedValue($request);
            $finalParams[$param->name] = $param;
        }

        foreach ($finalParams as $param){
            $rules = $param->validate;
            /** @var AbstractValidator $rule */
            foreach ($rules as $rule){
                $rule->allCheckParams($finalParams);
                $ret = $rule->execute($param,$request);
                if(!$ret){
                    $msg = $rule->errorMsg();
                    $ex = new ValidateFail($msg);
                    $ex->setFailRule($rule);
                    throw $ex;
                }
            }
        }
        return $finalParams;
    }
}