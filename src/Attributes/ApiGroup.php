<?php

namespace EasySwoole\HttpAnnotation\Attributes;

#[\Attribute(\Attribute::TARGET_CLASS)]
class ApiGroup
{
    function __construct(public string $groupName, public Description|string|null $description = null){}
}