<?php

namespace EasySwoole\HttpAnnotation\Attributes;


use EasySwoole\HttpAnnotation\Exception\Annotation;

class Example
{
    function __construct(
        public ?array $params = [],
        public ?Description $description = null,
        public ?string $plainText = null
    ){
        if(!empty($this->params) && $this->plainText != null){
            throw new Annotation("can not set params and plainText value at the same time");
        }
    }
}