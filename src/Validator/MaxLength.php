<?php

namespace EasySwoole\HttpAnnotation\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
use Psr\Http\Message\ServerRequestInterface;


class MaxLength extends AbstractValidator
{
    protected int $maxLen;

    function __construct(int $maxLen,string|null $errorMsg = null)
    {
        if(empty($errorMsg)){
            $errorMsg = "{#name} max length is {#maxLen}";
        }
        $this->errorMsgTpl($errorMsg);
        $this->maxLen = $maxLen;
    }

    protected function validate(ValidateRequest $validateRequest): bool
    {
        $itemData = $validateRequest->validateParam->parsedValue();
        if (is_numeric($itemData) || is_string($itemData)) {
            return strlen($itemData) <= $this->maxLen;
        }
        if (is_array($itemData) && (count($itemData) <= $this->maxLen)) {
            return true;
        }
        return  false;
    }

    function ruleName(): string
    {
        return "MaxLength";
    }
}