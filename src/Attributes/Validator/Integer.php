<?php

namespace EasySwoole\HttpAnnotation\Attributes\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use Psr\Http\Message\ServerRequestInterface;

class Integer extends AbstractValidator
{
    function __construct(?string $errorMsg = null)
    {
        if(empty($errorMsg)){
            $errorMsg = "{#name} must be integer";
        }
        $this->errorMsg($errorMsg);
    }


    protected function validate(Param $param, ServerRequestInterface $request): bool
    {
        if (is_integer($param->parsedValue())) {
            return filter_var($param->parsedValue(), FILTER_VALIDATE_INT) !== false;
        } else {
            return false;
        }
    }

    function ruleName(): string
    {
        return "Integer";
    }
}