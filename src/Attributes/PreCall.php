<?php

namespace EasySwoole\HttpAnnotation\Attributes;

use EasySwoole\HttpAnnotation\Enum\HttpMethod;

#[\Attribute]
class PreCall
{
    public $call;
    function __construct(callable $func)
    {
        $this->call = $func;
    }
}