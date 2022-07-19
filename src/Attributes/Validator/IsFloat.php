<?php

namespace EasySwoole\HttpAnnotation\Attributes\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use Psr\Http\Message\ServerRequestInterface;

class IsFloat extends AbstractValidator
{
    function __construct(?string $errorMsg = null)
    {
        if(empty($errorMsg)){
            $errorMsg = "{#name} must be float";
        }
        $this->errorMsg($errorMsg);
    }

    protected function validate(Param $param, ServerRequestInterface $request): bool
    {
        if (is_float($param->parsedValue())) {
            return filter_var($param->parsedValue(), FILTER_VALIDATE_FLOAT) !== false;
        } else {
            return false;
        }
    }

    function ruleName(): string
    {
        return "IsFloat";
    }
}