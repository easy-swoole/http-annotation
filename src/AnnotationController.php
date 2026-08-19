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
use EasySwoole\HttpAnnotation\Exception\ParamValidateFail;
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
            //必须使用allParams中的对象
            $allParams = $actionArgsInTag + $onRequestArgsInTag;
            foreach ($allParams as $paramName => $param){
                $param = clone $param;
                $allParams[$paramName] = $param;
                $param->parsedValue($this->request());
            }

            foreach ($onRequestArgsInTag as $param){
                $req = new ValidateRequest($allParams[$param->name]);
                $req->callClass = static::class;
                $req->callMethod = $this->getActionName();
                $req->request = $this->request();
                Utility::validateParam($req);
                $onRequestArg[$param->name] = $allParams[$param->name]->parsedValue();
            }

            foreach ($actionArgsInTag as $param){
                $req = new ValidateRequest($allParams[$param->name]);
                $req->callClass = static::class;
                $req->callMethod = $this->getActionName();
                $req->request = $this->request();

                Utility::validateParam($req);
            }

            var_dump($onRequestArg);
        }
        parent::__hook($actionArg,$onRequestArg);
    }
}