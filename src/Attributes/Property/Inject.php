<?php

namespace EasySwoole\HttpAnnotation\Attributes\Property;

#[\Attribute]
class Inject
{
    function __construct(public object $object){}
}