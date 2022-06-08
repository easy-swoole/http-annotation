<?php

namespace EasySwoole\HttpAnnotation\Attributes;

#[\Attribute]
class ApiGroup
{
    function __construct(public string $name, public ?Description $description = null){}
}