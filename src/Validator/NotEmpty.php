<?php

namespace EasySwoole\HttpAnnotation\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
use Psr\Http\Message\ServerRequestInterface;

class NotEmpty extends AbstractValidator
{
    function __construct(string|null $errorMsg = null)
    {
        if(empty($errorMsg)){
            $errorMsg = "{#name} is notEmpty";
        }
        $this->errorMsgTpl($errorMsg);
    }

    protected function validate(ValidateRequest $validateRequest): bool
    {
        $itemData = $validateRequest->validateParam->parsedValue();
        if ($itemData === 0 || $itemData === '0') {
            return true;
        }

        return !empty($itemData);
    }

    function ruleName(): string
    {
        return "NotEmpty";
    }
}