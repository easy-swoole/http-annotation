<?php

namespace EasySwoole\HttpAnnotation;

use EasySwoole\Component\Context\ContextManager;
use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Http\ReflectionCache;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Attributes\Property\Context;
use EasySwoole\HttpAnnotation\Attributes\Property\Di;
use EasySwoole\HttpAnnotation\Attributes\Property\Inject;
use EasySwoole\HttpAnnotation\Attributes\Validator\AbstractValidator;
use EasySwoole\HttpAnnotation\Enum\HttpMethod;
use EasySwoole\HttpAnnotation\Exception\Annotation;
use EasySwoole\HttpAnnotation\Exception\ParamError;
use EasySwoole\HttpAnnotation\Exception\RequestMethodNotAllow;
use EasySwoole\HttpAnnotation\Exception\ValidateFail;
use EasySwoole\Component\Di as IOC;


abstract class AnnotationController extends Controller
{
    public function __hook(?array $actionArg = [],?array $onRequestArg = null)
    {
        try{
            $this->preHandleProperty();
            $actionParams = $this->runParamsValidate($this->getActionName(),$this->request());
            $handler = function (Param $param)use(&$handler){
                if(!empty($param->subObject)){
                    $temp = [];
                    /** @var Param $item */
                    foreach ($param->subObject as $item){
                        $temp[$item->name] = $handler($item);
                    }
                    return $temp;
                }else{
                    return $param->parsedValue();
                }
            };
            /** @var Param $actionParam */
            foreach ($actionParams as $actionParam){
                $actionParams[$actionParam->name] = $handler($actionParam);
            }
            $ref = ReflectionCache::getInstance()->getClassReflection(static::class);
            if($ref->hasMethod($this->getActionName())){
                $ref = $ref->getMethod($this->getActionName());
                $type = null;
                $parameters = $ref->getParameters();
                if(!empty($parameters)){
                    //如果用数组来接收全部参数
                    $type = $parameters[0]->getType();
                    if($type){
                        $type = $type->getName();
                    }
                }
                if(count($parameters) == 1 && $type == "array"){
                    $key = $parameters[0]->name;
                    $actionArg[$key] = $actionParams;
                }else{
                    foreach ($parameters as $parameter){
                        $key = $parameter->name;
                        if(key_exists($key,$actionParams)){
                            $actionArg[$key] = $actionParams[$key];
                        }else{
                            throw new ParamError("method {$this->getActionName()}() require arg: {$key} , but not define in any controller annotation");
                        }
                    }
                }
            }
        }catch (\Throwable $exception){
            $this->onException($exception);
            return ;
        }
        if($onRequestArg == null){
            $onRequestArg = $actionParams;
        }
        parent::__hook($actionArg,$onRequestArg);
    }

    private function runParamsValidate(string $method, Request $request):array
    {

        $ref = ReflectionCache::getInstance()->getClassReflection(static::class);
        $allowMethod = ReflectionCache::getInstance()->allowMethodReflections($ref);
        //如果是存在于用户定义的方法，才允许缓存起来。
        //不然无限制方法会引起内存溢出
        if(!isset($allowMethod[$method])){
            return [];
        }

        $actionParams = AttributeCache::getInstance()->getClassMethodFullParams(static::class,$method);
        if($actionParams === null){
            $actionParams = [];
            $onRequestParams = $ref->getMethod("onRequest")->getAttributes(Param::class);
            $controllerGlobalParams = [];
            $gTemp = $ref->getAttributes(Param::class);
            foreach ($gTemp as $g){
                $args = $g->getArguments();
                try{
                    $test = new Param(...$args);
                    $controllerGlobalParams[$test->name] = $test;
                }catch (\Throwable $exception){
                    $controller = static::class;
                    $msg = "{$exception->getMessage()} in controller: {$controller} global param";
                    throw new Annotation($msg);
                }
            }

            $cacheActionParamKeys = [];

            if($ref->hasMethod($method)){
                $actionMethodRef = $ref->getMethod($method);
                $actionApiTags = $actionMethodRef->getAttributes(Api::class);
                if(!empty($actionApiTags)){
                    try{
                        $apiTag = new Api(...$actionApiTags[0]->getArguments());
                    }catch (\Throwable $exception){
                        $class = static::class;
                        $msg = "{$exception->getMessage()} in controller: {$class} method: {$method}";
                        throw new Annotation($msg);
                    }

                    /** @var Param $item */
                    foreach ($apiTag->requestParam as $item){
                        $actionParams[$item->name] = $item;
                        $cacheActionParamKeys[$item->name] = $item->name;
                    }

                    if($apiTag->allowMethod instanceof HttpMethod){
                        $allowRequestMethod = [$apiTag->allowMethod];
                    }else{
                        $allowRequestMethod = $apiTag->allowMethod;
                    }
                    $currentRequestMethod = $request->getMethod();
                    $test = constant(HttpMethod::class."::".$currentRequestMethod);
                    if(!in_array($test,$allowRequestMethod)){
                        throw new RequestMethodNotAllow("http {$currentRequestMethod} method is not allow for this request");
                    }
                }
            }

            foreach ($onRequestParams as $onRequestParam){
                $args = $onRequestParam->getArguments();
                try{
                    $onRequestParam = new Param(...$args);
                }catch (\Throwable $exception){
                    $controller = static::class;
                    $msg = "{$exception->getMessage()} in controller: {$controller} onRequest Method";
                    throw new Annotation($msg);
                }

                if(!isset($actionParam[$onRequestParam->name])){
                    $actionParam[$onRequestParam->name] = $onRequestParam;
                }
            }

            //全局定义的重复参数名，优先度低于method声明的
            foreach ($controllerGlobalParams as $param){
                if(!in_array($method,$param->ignoreAction)){
                    if(!isset($actionParams[$param->name])){
                        $actionParams[$param->name] = $param;
                        $cacheActionParamKeys[$param->name] = $param->name;
                    }
                }
            }
            AttributeCache::getInstance()->setClassMethodFullParams(static::class,$method,$actionParams);
            AttributeCache::getInstance()->setClassMethodParams(static::class,$method,$cacheActionParamKeys);
        }

        $preHandler = function (Param $param)use(&$preHandler,$request){
            //这边需要进行克隆再进行真实值的解析
            $temp = clone $param;
            $subItems = [];
            foreach ($temp->subObject as $item){
                $subItems[] = $preHandler($item);
            }
            $temp->subObject = $subItems;
            return $temp;
        };

        $finalParams = [];
        /** @var Param $param */
        foreach ($actionParams as $param){
            if(!in_array($method,$param->ignoreAction)){
                $finalParams[$param->name] = $preHandler($param);
            }
        }

        $validateFunc = function (Param $param)use($request,$finalParams,&$validateFunc){
            //当有下级的时候，当级校验没有意义
            if(!empty($param->subObject)){
                foreach ($param->subObject as $sub){
                    $validateFunc($sub);
                }
            }else{
                $param->parsedValue($request);
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
        };

        foreach ($finalParams as $param){
            $validateFunc($param);
        }
        return $finalParams;
    }

    private function preHandleProperty()
    {
        $ref = ReflectionCache::getInstance()->getClassReflection(static::class);
        $list = $ref->getProperties(\ReflectionProperty::IS_PUBLIC|\ReflectionProperty::IS_PROTECTED|\ReflectionProperty::IS_PRIVATE);
        foreach ($list as $item){
            if(!$item->isStatic() && !$item->isReadOnly()){
                $attrs = $item->getAttributes();
                if(count($attrs) > 1){
                    throw new Annotation("only allow one annotation attribute for property : {$item->name} in class ".static::class);
                }
                $name = $item->name;
                foreach ($attrs as $attr){
                    switch ($attr->getName()){
                        case Inject::class:{
                            $this->$name = $attr->getArguments()['object'];
                            break;
                        }
                        case Di::class:{
                            $this->$name = IOC::getInstance()->get($attr->getArguments()['key']);
                            break;
                        }

                        case Context::class:{
                            $this->$name = ContextManager::getInstance()->get($attr->getArguments()['key']);
                            break;
                        }
                    }
                }
            }
        }
    }
}