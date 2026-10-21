<?php

namespace EasySwoole\HttpAnnotation\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
use Psr\Http\Message\ServerRequestInterface;

class Timestamp extends AbstractValidator
{

    function __construct(string|null $errorMsg = null)
    {
        if ($errorMsg !== null) {
            $this->errorMsgTpl($errorMsg);
        }
    }

    protected function validate(ValidateRequest $validateRequest): bool
    {
        $itemData = $validateRequest->validateParam->parsedValue();
        if (!is_int($itemData) && !is_string($itemData)) {
            return false;
        }
        // 先验证整数范围，避免日期函数隐式截断小数或接收溢出的数值字符串。
        $timestamp = filter_var($itemData, FILTER_VALIDATE_INT);
        if ($timestamp === false) {
            return false;
        }
        if (strtotime(date('d-m-Y H:i:s', $timestamp)) === $timestamp) {
            return true;
        }

        return false;
    }

    public static function ruleName(): string
    {
        return "Timestamp";
    }
}
