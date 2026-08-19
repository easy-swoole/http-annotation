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
        if(empty($errorMsg)){
            $errorMsg = "{#name} must be timestamp";
        }
        $this->errorMsgTpl($errorMsg);
    }

    protected function validate(ValidateRequest $validateRequest): bool
    {
        $itemData = $validateRequest->validateParam->parsedValue();
        if (!is_numeric($itemData)) {
            return false;
        }

        if (strtotime(date('d-m-Y H:i:s', $itemData)) === (int)$itemData) {
            return true;
        }

        return false;
    }

    function ruleName(): string
    {
        return "Timestamp";
    }
}