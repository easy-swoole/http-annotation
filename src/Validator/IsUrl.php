<?php

namespace EasySwoole\HttpAnnotation\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
use Psr\Http\Message\ServerRequestInterface;

class IsUrl extends AbstractValidator
{
    function __construct(string|null $errorMsg = null)
    {
        if(empty($errorMsg)){
            $errorMsg = "{#name} must be a url";
        }
        $this->errorMsgTpl($errorMsg);
    }

    protected function validate(ValidateRequest $validateRequest): bool
    {
        $itemData = $validateRequest->validateParam->parsedValue();
        if (!is_string($itemData)) {
            return false;
        }

        if (!filter_var($itemData, FILTER_VALIDATE_URL)) {
            return false;
        }
        return true;
    }

    function ruleName(): string
    {
        return 'IsUrl';
    }
}