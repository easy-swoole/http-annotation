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
        $value = $validateRequest->validateParam->parsedValue();
        if ((!is_string($value) && !is_int($value) && !is_float($value))
            || (is_float($value) && !is_finite($value))) {
            return false;
        }
        return (bool)preg_match('/^[a-zA-Z\-\_]+$/', (string)$value);
    }

    public static function ruleName(): string
    {
        return "AlphaDash";
    }
}
