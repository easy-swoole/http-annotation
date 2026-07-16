<?php

namespace EasySwoole\HttpAnnotation\Attributes;

use EasySwoole\HttpAnnotation\Enum\HttpMethod;

#[\Attribute]
class PreCall
{
    public $call;
    public string|null $injectParamName = null;
    function __construct(callable $call,string|null $injectParamName = null)
    {
        $this->call = $call;
        $this->injectParamName = $injectParamName;
    }
}