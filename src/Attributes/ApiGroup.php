<?php

namespace EasySwoole\HttpAnnotation\Attributes;

#[\Attribute()]
class ApiGroup
{
    function __construct(public string $groupName, public Description|string|null $description = null){}
}