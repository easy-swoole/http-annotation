<?php

namespace EasySwoole\HttpAnnotation\Attributes;

#[\Attribute]
class Validator
{
    const required = "required";
    public function __construct(array $rule,?string $message = null){}
}