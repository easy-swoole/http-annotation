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
        if ($errorMsg !== null) {
            $this->errorMsgTpl($errorMsg);
        }
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

    public static function ruleName(): string
    {
        return "DateFormat";
    }
}