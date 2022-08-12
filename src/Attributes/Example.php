<?php

namespace EasySwoole\HttpAnnotation\Attributes;

use EasySwoole\HttpAnnotation\Exception\Annotation;

class Example
{   public ?array $params = [];
    public ?Description $description = null;
    function __construct(...$args){
        foreach ($args as $arg){
            if($arg instanceof Param){
                $this->params[] = $arg;
            }else if($arg instanceof Description){
                if($this->description){
                    throw new Annotation("can not reset example description");
                }
                $this->description = $arg;
            }else{
                throw new Annotation("example param type error , only allow Description and Param instance");
            }
        }
    }
}