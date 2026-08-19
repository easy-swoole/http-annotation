<?php

namespace EasySwoole\HttpAnnotation;

use EasySwoole\Component\Context\ContextManager;
use EasySwoole\Component\Di as IOC;
use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Http\ReflectionCache;
use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Attributes\PreCall;
use EasySwoole\HttpAnnotation\Attributes\Property\Context;
use EasySwoole\HttpAnnotation\Attributes\Property\Di;
use EasySwoole\HttpAnnotation\Attributes\Property\Inject;
use EasySwoole\HttpAnnotation\Enum\HttpMethod;
use EasySwoole\HttpAnnotation\Exception\Annotation;
use EasySwoole\HttpAnnotation\Exception\ParamError;
use EasySwoole\HttpAnnotation\Exception\RequestMethodNotAllow;
use EasySwoole\HttpAnnotation\Exception\ValidateFail;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use EasySwoole\Http\Context as HttpContext;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;


abstract class AnnotationController extends Controller
{
    public function __hook(array|null $actionArg = [],array|null $onRequestArg = null)
    {
        $attributeInfo = AttributeCache::getInstance()->parseClass(static::class);
        $apiTag = $attributeInfo->apiTag($this->getActionName());
        if($apiTag){
            HttpContext::getInstance()->set(HttpContext::KEY_HTTP_REQUEST,$this->request());

            $actionArg = [];
            $onRequestArg = [];

            $actionArgsInTag = $apiTag->requestParam;
            $onRequestArgsInTag = $attributeInfo->onRequest->onRequestParams;
            //如果有重复定义，则覆盖onRequest参数
            /** @var Param $param */
            foreach ($actionArgsInTag as $param){
                if(isset($onRequestArgsInTag[$param->name])){
                    $onRequestArgsInTag[$param->name] = $param;
                }
            }
            $allParams = $actionArgsInTag + $onRequestArgsInTag;
            foreach ($allParams as $paramName => $param){
                $param = clone $param;
                $allParams[$paramName] = $param;
                $param->parsedValue($this->request());
            }
            foreach ($allParams as $param){
                $this->validateParam($param,$allParams);
            }

        }
        parent::__hook($actionArg,$onRequestArg);
    }

    private function validateParam(Param $param,array $allParams)
    {
        //当有下级的时候，当级校验没有意义
        if(!empty($param->subObject)){
            foreach ($param->subObject as $sub){
                $this->validateParam($sub,$allParams);
            }
        }else{
            $req = new ValidateRequest(
                validateParam: $param,
                request: $this->request(),
                allDefineParams: $allParams
            );
            $rules = $param->validate;
            /** @var AbstractValidator $rule */
            foreach ($rules as $rule){
                $ret = $rule->execute($req);
            }
        }
    }
}