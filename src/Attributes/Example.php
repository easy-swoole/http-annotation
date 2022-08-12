<?php

namespace EasySwoole\HttpAnnotation\Attributes;

use EasySwoole\HttpAnnotation\Exception\Annotation;

class Example
{
    function __construct(
        public ?array $params = [],
        public ?Description $description = null
    ){}
}