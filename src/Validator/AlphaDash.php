<?php

namespace EasySwoole\HttpAnnotation\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
use Psr\Http\Message\ServerRequestInterface;

class AlphaDash extends AbstractValidator
{
    function __construct(string|null $errorMsg = null)
    {
        if ($errorMsg !== null) {
            $this->errorMsgTpl($errorMsg);
        }
    }


    protected function validate(ValidateRequest $validateRequest): bool
    {
        return (bool)preg_match( '/^[a-zA-Z\-\_]+$/', (string)$validateRequest->validateParam->parsedValue());
    }

    public static function ruleName(): string
    {
        return "AlphaDash";
    }
}