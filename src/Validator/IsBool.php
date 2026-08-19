<?php

namespace EasySwoole\HttpAnnotation\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
use Psr\Http\Message\ServerRequestInterface;

class IsBool extends AbstractValidator
{
    function __construct(string|null $errorMsg = null)
    {
        if(empty($errorMsg)){
            $errorMsg = "{#name} must be bool";
        }
        $this->errorMsgTpl($errorMsg);
    }

    protected function validate(ValidateRequest $validateRequest): bool
    {
        return in_array($validateRequest->validateParam->parsedValue(),["1",1,"0",0,true,false],true);
    }

    function ruleName(): string
    {
        return "IsBool";
    }
}