<?php

namespace EasySwoole\HttpAnnotation\Attributes;

use EasySwoole\HttpAnnotation\Enum\HttpMethod;

#[\Attribute]
class PreCall
{
    public $call;
    public ?string $injectParamName = null;
    function __construct(callable $call,string $injectParamName = null)
    {
        $this->call = $call;
        $this->injectParamName = $injectParamName;
    }
}