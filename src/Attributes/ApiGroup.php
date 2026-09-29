<?php

namespace EasySwoole\HttpAnnotation\Attributes;

#[\Attribute(\Attribute::TARGET_CLASS)]
class ApiGroup
{
    public string $relateClass;

    function __construct(public string $groupName, public string|null $description = null){}
}