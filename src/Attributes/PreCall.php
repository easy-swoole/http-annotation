<?php

namespace EasySwoole\HttpAnnotation\Attributes;

use EasySwoole\HttpAnnotation\Enum\HttpMethod;

#[\Attribute]
class PreCall
{
    public $call;
    function __construct(callable $call)
    {
        $this->call = $call;
    }
}