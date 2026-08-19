<?php

namespace EasySwoole\HttpAnnotation\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
use Psr\Http\Message\ServerRequestInterface;

class IsIp extends AbstractValidator
{
    function __construct(string|null $errorMsg = null)
    {
        if(empty($errorMsg)){
            $errorMsg = "{#name} must be a ip format";
        }
        $this->errorMsgTpl($errorMsg);
    }

    protected function validate(ValidateRequest $validateRequest): bool
    {
        return filter_var($validateRequest->validateParam->parsedValue(), FILTER_VALIDATE_IP) !== false;
    }

    function ruleName(): string
    {
        return "IsIp";
    }
}