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
    public function __hook(array $actionArg = [])
    {
        try{
            $this->preAnnotationHook();
            $ret = $this->runAnnotationHook($this->getActionName(),$this->request());
            $ref = ReflectionCache::getInstance()->getClassReflection(static::class);
            if($ref->hasMethod($this->getActionName())){
                $ref = $ref->getMethod($this->getActionName());
                foreach ($ref->getParameters() as $parameter){
                    $key = $parameter->name;
                    if(isset($ret[$key])){
                        $actionArg[$key] = $ret[$key]->parsedValue();
                    }else{
                        throw new ParamError("method {$this->getActionName()}() require arg: {$key} , but not define in any controller annotation");
                    }
                }
            }
        }catch (\Throwable $exception){
            $this->onException($exception);
            return ;
        }

        parent::__hook($actionArg);
    }

    private function runAnnotationHook(string $method,Request $request):array
    {

        $actionParams = AttributeCache::getInstance()->getClassMethodMap(static::class,$method);
        if($actionParams === null){
            $actionParams = [];
            $ref = ReflectionCache::getInstance()->getClassReflection(static::class);
            $onRequestParams = $ref->getMethod("onRequest")->getAttributes(Param::class);

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

                    $actionParams = $apiTag->requestParam;
                    if($apiTag->allowMethod instanceof HttpMethod){
                        $allowRequestMethod = [$apiTag->allowMethod->toString()];
                    }else{
                        $allowRequestMethod = $apiTag->allowMethod;
                    }
                    $currentRequestMethod = $request->getMethod();
                    if(!in_array($currentRequestMethod,$allowRequestMethod)){
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

    private function preAnnotationHook()
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