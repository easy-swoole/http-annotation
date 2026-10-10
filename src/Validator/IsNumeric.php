<?php

namespace EasySwoole\HttpAnnotation\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
use Psr\Http\Message\ServerRequestInterface;

class IsNumeric extends AbstractValidator
{
    function __construct(string|null $errorMsg = null)
    {
        if ($errorMsg !== null) {
            $this->errorMsgTpl($errorMsg);
        }
    }


    protected function validate(ValidateRequest $validateRequest): bool
    {
        if (is_numeric($validateRequest->validateParam->parsedValue())) {
            return true;
        } else {
            return false;
        }
    }

    public static function ruleName(): string
    {
        return "IsNumeric";
    }
}