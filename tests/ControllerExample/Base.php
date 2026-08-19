<?php

namespace EasySwoole\HttpAnnotation\Tests\ControllerExample;

use EasySwoole\HttpAnnotation\AnnotationController;
use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Exception\Annotation;
use EasySwoole\HttpAnnotation\Exception\ParamValidateFail;
use EasySwoole\HttpAnnotation\Validator\NotEmpty;

class Base extends AnnotationController
{
    protected function onException(\Throwable $throwable): void
    {
        if($throwable instanceof ParamValidateFail){
            $this->writeJson(400,null,$throwable->getMessage());
        }else{
            if($throwable instanceof Annotation){
                $this->writeJson(400,null,$throwable->getMessage());
            }else{
                throw $throwable;
            }
        }
    }

//    #[Param(
//        name: 'token',
//        validate: [
//
//        ]
//    )]
    function onRequest(?string $action): ?bool
    {
        return true;
    }
}