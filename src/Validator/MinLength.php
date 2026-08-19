<?php

namespace EasySwoole\HttpAnnotation\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
use Psr\Http\Message\ServerRequestInterface;


class MinLength extends AbstractValidator
{
    protected int $minLen;

    function __construct(int $minLen,string|null $errorMsg = null)
    {
        $this->minLen = $minLen;
        if(empty($errorMsg)){
            $errorMsg = "{#name} min length is {#minLen}";
        }
        $this->errorMsgTpl($errorMsg);
    }

    protected function validate(ValidateRequest $validateRequest): bool
    {
        $itemData = $validateRequest->validateParam->parsedValue();
        if (is_numeric($itemData) || is_string($itemData)) {
            return strlen($itemData) >= $this->minLen;
        }
        if (is_array($itemData) && (count($itemData) >= $this->minLen)) {
            return true;
        }
        return  false;
    }

    function ruleName(): string
    {
        return "MinLength";
    }
}