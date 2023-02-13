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
            $apiTag = AttributeCache::getInstance()->getClassMethodApiTag(static::class,$this->getActionName());
            if($apiTag){
                if($apiTag->allowMethod instanceof HttpMethod){
                    $allowRequestMethod = [$apiTag->allowMethod];
                }else{
                    $allowRequestMethod = $apiTag->allowMethod;
                }
                $currentRequestMethod = $this->request()->getMethod();
                $test = constant(HttpMethod::class."::".$currentRequestMethod);
                if(!in_array($test,$allowRequestMethod)){
                    throw new RequestMethodNotAllow("http {$currentRequestMethod} method is not allow for this request");
                }
            }

            $this->preHandleProperty();
            $onRequestArg = $this->runParamsValidate($this->getActionName(),$this->request());
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
            foreach ($onRequestArg as $actionParam){
                $onRequestArg[$actionParam->name] = $handler($actionParam);
            }
            $ref = ReflectionCache::getInstance()->getClassReflection(static::class);
            if($ref->hasMethod($this->getActionName())){
                $methodRef = $ref->getMethod($this->getActionName());
                $type = null;
                $parameters = $methodRef->getParameters();
                if(!empty($parameters)){
                    //如果用数组来接收全部参数
                    $type = $parameters[0]->getType();
                    if($type){
                        $type = $type->getName();
                    }
                }
                if(count($parameters) == 1 && $type == "array"){
                    $key = $parameters[0]->name;
                    //传递全部参数的时候，仅仅保留函数定义的参数。
                    $temps = Utility::parseMethodParams($ref,$this->getActionName());
                    foreach ($temps as $keyKey => $temp){
                        $temps[$keyKey] = $onRequestArg[$keyKey] !== null ? $onRequestArg[$keyKey] : null;
                    }
                    $actionArg[$key] = $temps;
                }else{
                    foreach ($parameters as $parameter){
                        $key = $parameter->name;
                        if(key_exists($key,$onRequestArg)){
                            $actionArg[$key] = $onRequestArg[$key];
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
        /*
        * $onRequestArg 是全部定义的参数，而$actionArg 是方法定义参数
        */
        parent::__hook($actionArg,$onRequestArg);
    }

    private function runParamsValidate(string $method, Request $request):array
    {
        $ref = ReflectionCache::getInstance()->getClassReflection(static::class);
        $actionParams = Utility::parseActionParams($ref,$method);

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
//            if(!in_array($method,$param->ignoreAction)){
//                $finalParams[$param->name] = $preHandler($param);
//            }
            $finalParams[$param->name] = $preHandler($param);
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