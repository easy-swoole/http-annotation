<?php

namespace EasySwoole\HttpAnnotation\Attributes;

class Example
{
    function __construct(
        public ?array $params = null,
        public ?Description $description = null
    ){}
}