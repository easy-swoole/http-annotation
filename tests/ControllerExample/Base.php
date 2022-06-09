<?php

namespace EasySwoole\HttpAnnotation\Tests\ControllerExample;

use EasySwoole\HttpAnnotation\AnnotationController;
use EasySwoole\HttpAnnotation\Exception\Annotation;
use EasySwoole\HttpAnnotation\Exception\ValidateFail;

class Base extends AnnotationController
{
    protected function onException(\Throwable $throwable): void
    {
        if($throwable instanceof ValidateFail){
            $this->writeJson(400,null,$throwable->getMessage());
        }else{
            if($throwable instanceof Annotation){
                $this->writeJson(400,null,$throwable->getMessage());
            }else{
                throw $throwable;
            }
        }
    }
}