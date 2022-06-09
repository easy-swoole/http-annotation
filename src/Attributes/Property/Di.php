<?php

namespace EasySwoole\HttpAnnotation\Attributes\Property;

#[\Attribute]
class Di
{
    function __construct(public string $key){}
}