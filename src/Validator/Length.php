<?php

namespace EasySwoole\HttpAnnotation\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
use Psr\Http\Message\ServerRequestInterface;

class Length extends AbstractValidator
{
    protected int $length;
    function __construct(int $length,string|null $errorMsg = null)
    {
        if(empty($errorMsg)){
            $errorMsg = "{#validateParam} length must be {#length}";
        }
        $this->errorMsgTpl($errorMsg);
        $this->length = $length;
    }


    protected function validate(ValidateRequest $validateRequest): bool
    {
        $itemData = $validateRequest->validateParam->parsedValue();
        if (is_numeric($itemData) || is_string($itemData)) {
            return strlen($itemData) == $this->length;
        }
        if (is_array($itemData) && (count($itemData) == $this->length)) {
            return true;
        }
        return false;
    }

    function ruleName(): string
    {
        return "Length";
    }
}