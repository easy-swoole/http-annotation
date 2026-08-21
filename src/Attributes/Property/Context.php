<?php

namespace EasySwoole\HttpAnnotation\Attributes\Property;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Context
{
    function __construct(public string $key){}
}