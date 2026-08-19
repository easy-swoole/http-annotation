<?php

namespace EasySwoole\HttpAnnotation\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
use Psr\Http\Message\ServerRequestInterface;

class Integer extends AbstractValidator
{
    function __construct(string|null $errorMsg = null)
    {
        if(empty($errorMsg)){
            $errorMsg = "{#validateParam} must be integer";
        }
        $this->errorMsgTpl($errorMsg);
    }


    protected function validate(ValidateRequest $validateRequest): bool
    {
        return filter_var($validateRequest->validateParam->parsedValue(), FILTER_VALIDATE_INT) !== false;
    }

    function ruleName(): string
    {
        return "Integer";
    }
}