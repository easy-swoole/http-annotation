<?php

namespace EasySwoole\HttpAnnotation\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
use Psr\Http\Message\ServerRequestInterface;

class OptionalIfParamMiss extends AbstractValidator
{
    protected string $paramName;

    function __construct(string $paramName,string|null $errorMsg = null)
    {
        $this->paramName = $paramName;
        if(empty($errorMsg)){
            $errorMsg = "{#name} is optional when param {$paramName} miss";
        }
        $this->errorMsgTpl($errorMsg);
    }

    protected function validate(ValidateRequest $validateRequest): bool
    {
        return true;
    }

    function ruleName(): string
    {
        return 'OptionalIfParamMiss';
    }
}