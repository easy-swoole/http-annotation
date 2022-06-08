<?php

namespace EasySwoole\HttpAnnotation\Attributes\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use Psr\Http\Message\ServerRequestInterface;

#[\Attribute]
class Optional extends AbstractValidator
{
    protected function validate(Param $param,ServerRequestInterface $request): bool
    {
        return true;
    }

    function ruleName(): string
    {
        return "Optional";
    }
}