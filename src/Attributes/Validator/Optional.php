<?php

namespace EasySwoole\HttpAnnotation\Attributes\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use Psr\Http\Message\ServerRequestInterface;


class Optional extends AbstractValidator
{
    function __construct(?string $errorMsg = null)
    {
        if(empty($errorMsg)){
            $errorMsg = "{#name} is optional";
        }
        $this->errorMsg($errorMsg);
    }

    protected function validate(Param $param,ServerRequestInterface $request): bool
    {
        return true;
    }

    function ruleName(): string
    {
        return "Optional";
    }
}