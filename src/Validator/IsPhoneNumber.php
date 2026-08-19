<?php

namespace EasySwoole\HttpAnnotation\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
use Psr\Http\Message\ServerRequestInterface;

class IsPhoneNumber extends AbstractValidator
{

    function __construct(string|null $errorMsg = null)
    {
        if(empty($errorMsg)){
            $errorMsg = "{#validateParam} must be phone number";
        }
        $this->errorMsgTpl($errorMsg);
    }
    protected function validate(ValidateRequest $validateRequest): bool
    {
        return (bool)preg_match( '/^1[3456789]\d{9}$/', (string)$validateRequest->validateParam->parsedValue());
    }

    function ruleName(): string
    {
        return 'IsPhoneNumber';
    }
}