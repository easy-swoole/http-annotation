<?php

namespace EasySwoole\HttpAnnotation\Validator;

use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;

class Integer extends AbstractValidator
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
        // 与 INT 转换保持一致，拒绝会被 filter_var 隐式接受的布尔值和浮点值。
        return (is_int($value) || is_string($value))
            && filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    public static function ruleName(): string
    {
        return "Integer";
    }
}
