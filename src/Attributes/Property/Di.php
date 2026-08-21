<?php

namespace EasySwoole\HttpAnnotation\Attributes\Property;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Di
{
    function __construct(public string $key){}
}