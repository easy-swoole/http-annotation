<?php

namespace EasySwoole\HttpAnnotation\Attributes;

#[\Attribute()]
class ApiGroup
{
    function __construct(public string $groupName, public ?Description $description = null){}
}