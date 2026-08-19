<?php

namespace EasySwoole\HttpAnnotation\Validator;

use DateTime;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
use Psr\Http\Message\ServerRequestInterface;

class DateFormat extends AbstractValidator
{
    private string $format;
    function __construct(string $dateFormat,string|null $errorMsg = null)
    {
        $this->format = $dateFormat;
        if(empty($errorMsg)){
            $errorMsg = "{#name} must be date format {#format}";
        }
        $this->errorMsgTpl($errorMsg);
    }

    protected function validate(ValidateRequest $validateRequest): bool
    {
        $itemData = $validateRequest->validateParam->parsedValue();
        if(empty($itemData)){
            return false;
        }

        $test = DateTime::createFromFormat($this->format, $itemData);

        if($test){
            return true;
        }

        return false;
    }

    function ruleName(): string
    {
        return "DateFormat";
    }
}