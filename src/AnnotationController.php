<?php

namespace EasySwoole\HttpAnnotation;

use EasySwoole\Component\Context\ContextManager;
use EasySwoole\Component\Di;
use EasySwoole\Component\Di as IOC;
use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Http\ReflectionCache;
use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Attributes\PreCall;
use EasySwoole\HttpAnnotation\Bean\PropertyAttribute;
use EasySwoole\HttpAnnotation\Enum\HttpMethod;
use EasySwoole\HttpAnnotation\Exception\Annotation;
use EasySwoole\HttpAnnotation\Exception\ParamError;
use EasySwoole\HttpAnnotation\Exception\RequestMethodNotAllow;
use EasySwoole\HttpAnnotation\Exception\ParamValidateFail;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;


abstract class AnnotationController extends Controller
{
    public function __hook(array|null $actionArg = [],array|null $onRequestArg = null)
    {
        $attributeInfo = AttributeCache::getInstance()->parseClass(static::class);

        $preCalls = $attributeInfo->globalPreCall;
        /** @var PreCall $preCall */
        foreach ($preCalls as $preCall) {
            $ret = call_user_func($preCall->call,$this->getActionName(), $this->request(),$this->response());
            if($ret === false){
                return;
            }
        }

        $onRequestArg = [];
        $actionArgsInTag = [];
        $actionArg = [];
        $onRequestArgsInTag = $attributeInfo->onRequest->onRequestParams;
        $apiTag = $attributeInfo->apiTag($this->getActionName());
        if($apiTag){
            if($apiTag->allowMethod instanceof HttpMethod){
                $allowRequestMethod = [$apiTag->allowMethod];
            }else{
                $allowRequestMethod = $apiTag->allowMethod;
            }
            $currentRequestMethod = $this->request()->getMethod();
            $test = constant(HttpMethod::class."::".$currentRequestMethod);
            if(!in_array($test,$allowRequestMethod)){
                throw new RequestMethodNotAllow("{$currentRequestMethod} method is not allow for this request");
            }

            $actionArgsInTag = $apiTag->requestParam;
            //如果有重复定义，则覆盖onRequest参数
            /** @var Param $param */
            foreach ($actionArgsInTag as $param){
                if(isset($onRequestArgsInTag[$param->name])){
                    $onRequestArgsInTag[$param->name] = $param;
                }
            }
            $finalAllParams = $actionArgsInTag + $onRequestArgsInTag;
        }else{
            $finalAllParams = $onRequestArgsInTag;
        }
        //必须使用allParams中的对象
        foreach ($finalAllParams as $paramName => $param){
            if(!in_array($this->getActionName(),$param->ignoreAction)){
                $param = clone $param;
                $finalAllParams[$paramName] = $param;
                $param->parsedValue($this->request());
            }else{
                unset($finalAllParams[$paramName]);
            }
        }
        /** @var Param $param */
        foreach ($onRequestArgsInTag as $param){
            if(!isset($finalAllParams[$param->name])){
                continue;
            }
            $validateRequest = new ValidateRequest($finalAllParams[$param->name]);
            $validateRequest->callClass = static::class;
            $validateRequest->callMethod = $this->getActionName();
            $validateRequest->request = $this->request();
            Utility::validateParam($validateRequest);
            $onRequestArg[$param->name] = $finalAllParams[$param->name]->parsedValue();
        }

        foreach ($actionArgsInTag as $param){
            if(!isset($finalAllParams[$param->name])){
                continue;
            }
            $validateRequest = new ValidateRequest($finalAllParams[$param->name]);
            $validateRequest->callClass = static::class;
            $validateRequest->callMethod = $this->getActionName();
            $validateRequest->request = $this->request();
            Utility::validateParam($validateRequest);
        }
        
        if($apiTag){
            $methodRef = ReflectionCache::getInstance()->allowMethodReflection(static::class,$this->getActionName());
            $parameters = $methodRef->getParameters();
            if(!empty($parameters)){
                //如果用数组来接收全部参数
                $type = $parameters[0]->getType();
                if($type){
                    $type = $type->getName();
                }
                if(count($parameters) == 1 && $type == "array"){
                    $paramKey = $parameters[0]->name;
                    $temp = [];
                    foreach ($actionArgsInTag as $param){
                        /** @var Param $param */
                        $param = $finalAllParams[$param->name];
                        if($param->ignorePassArgWhenNotSet && !$param->hasSet()){
                            continue;
                        }
                        $temp[$param->name] = $param->parsedValue();
                    }
                    $actionArg[$paramKey] = $temp;
                }else{
                    foreach ($parameters as $parameter){
                        $key = $parameter->name;
                        if(key_exists($key,$finalAllParams)){
                            $actionArg[$key] = $finalAllParams[$key]->parsedValue();
                        }else{
                            throw new ParamError("method {$this->getActionName()}() require arg: {$key} , but not define in any controller annotation");
                        }
                    }
                }
            }
        }

        if(isset($attributeInfo->methodPreCall[$this->getActionName()])){
            $preCalls = $attributeInfo->methodPreCall[$this->getActionName()];
            /** @var PreCall $preCall */
            foreach ($preCalls as $preCall) {
                $ret = call_user_func($preCall->call, $this->request(),$this->response());
                if($ret === false){
                    return;
                }
            }
        }

        //处理Di Context
        $properties = $attributeInfo->propertyAttribute;
        /**
         * @var  $property
         * @var PropertyAttribute $propertyAttribute
         */
        foreach ($properties as $property => $propertyAttribute) {
            if($propertyAttribute->di){
                $this->{$property} = Di::getInstance()->get($propertyAttribute->di->key);
            }
            if($propertyAttribute->context){
                $this->{$property} = ContextManager::getInstance()->get($propertyAttribute->di->key);
            }
        }


        parent::__hook($actionArg,$onRequestArg);
    }
}