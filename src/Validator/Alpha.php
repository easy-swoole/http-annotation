<?php

namespace EasySwoole\HttpAnnotation\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
use Psr\Http\Message\ServerRequestInterface;

class Alpha extends AbstractValidator
{
    function __construct(string|null $errorMsg = null)
    {
        if(empty($errorMsg)){
            $errorMsg = "{#name} must be all alpha";
        }
        $this->errorMsgTpl($errorMsg);
    }


    protected function validate(ValidateRequest $validateRequest): bool
    {
        return (bool)preg_match( '/^[a-zA-Z]+$/', (string)$validateRequest->validateParam->parsedValue());
    }

    function ruleName(): string
    {
        return "Alpha";
    }
}