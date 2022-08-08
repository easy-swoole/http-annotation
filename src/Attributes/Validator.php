<?php

namespace EasySwoole\HttpAnnotation\Attributes;

#[\Attribute]
class Validator
{
    public function __construct(array $rule,?string $message = null){}
}