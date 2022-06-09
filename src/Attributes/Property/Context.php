<?php

namespace EasySwoole\HttpAnnotation\Attributes\Property;

#[\Attribute]
class Context
{
    function __construct(public string $key){}
}