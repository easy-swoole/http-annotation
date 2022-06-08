<?php

namespace EasySwoole\HttpAnnotation\Attributes\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use Psr\Http\Message\ServerRequestInterface;

class ActiveUrl extends AbstractValidator
{

    protected function validate(Param $param, ServerRequestInterface $request): bool
    {

    }

    function ruleName(): string
    {
        return "ActiveUrl";
    }
}