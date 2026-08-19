<?php

namespace EasySwoole\HttpAnnotation;

use EasySwoole\Http\ReflectionCache;
use EasySwoole\Http\UrlParser;
use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\ExtendParam;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Enum\HttpMethod;
use EasySwoole\HttpAnnotation\Exception\Annotation;
use EasySwoole\HttpAnnotation\Exception\ParamValidateFail;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
use EasySwoole\Utility\File;
use FastRoute\RouteCollector;
use Psr\Http\Message\ServerRequestInterface;

class Utility
{

    private static function getFileDeclaredClass(string $file): array
    {

        $namespace = null;
        $matchNamespace = false;
        $matchClass = false;
        $classes = [];
        foreach (token_get_all(file_get_contents($file)) as $line => $info){
            if(($info[0] == T_NAMESPACE) && $namespace === null){
                $matchNamespace = true;
                continue;
            }
            if(($info[0] == T_NAME_QUALIFIED) && $matchNamespace){
                $namespace = $info[1];
                $matchNamespace = false;
                continue;
            }
            if($info[0] == T_CLASS){
                $matchClass = true;
                continue;
            }
            if($matchClass && $info[0] == T_STRING){
                $classes[] = $info[1];
                $matchClass = false;
            }
        }

        $ret = [];

        foreach ($classes as $class){
            $class = ltrim($class,"\\");
            if($namespace !== null){
                $ret[] = $namespace."\\".$class;
            }else{
                $ret[] = $class;
            }
        }

        return $ret;

    }


    public static function validateParam(ValidateRequest $validateRequest,string|null $parentParamName = null): void
    {
        //当有下级的时候，当级校验没有意义
        if(!empty($validateRequest->validateParam->subObject)){
            if(empty($parentParamName)){
                $parentParamName = $validateRequest->validateParam->name;
            }else{
                $parentParamName .= ".{$validateRequest->validateParam->name}";
            }

            foreach ($validateRequest->validateParam->subObject as $sub){
                $t = clone $validateRequest;
                $t->validateParam = $sub;
                self::validateParam($t,$parentParamName);
            }
        }else{
            $rules = $validateRequest->validateParam->validate;
            /** @var AbstractValidator $rule */
            foreach ($rules as $rule){
                if(!$rule->execute($validateRequest)){
                    if(!empty($parentParamName)){
                        $parentParamName .= '.';
                    }else{
                        $parentParamName = '';
                    }
                    $parentParamName = $parentParamName.$validateRequest->validateParam->name;
                    $msg = $rule->errorMsg($parentParamName);
                    $ex = new ParamValidateFail("{$msg} in {$validateRequest->callClass} method {$validateRequest->callMethod}");
                    $ex->setFailRule($rule);
                    $ex->setParamName($parentParamName);

                    throw $ex;
                }
            }
        }
    }
}