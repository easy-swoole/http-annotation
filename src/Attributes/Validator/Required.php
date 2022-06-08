<?php

namespace EasySwoole\HttpAnnotation\Attributes\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use Psr\Http\Message\ServerRequestInterface;

#[\Attribute]
class Required extends AbstractValidator
{
    function __construct(?string $errorMsg = null)
    {
        $this->errorMsg($errorMsg);
    }

    protected function validate(Param $param,ServerRequestInterface $request): bool
    {
        if((!$param->isNullData()) && ($param->parsedValue() === null)){
            return false;
        }
        return true;
    }

    function ruleName(): string
    {
        return "Required";
    }
}