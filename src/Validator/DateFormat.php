<?php

namespace EasySwoole\HttpAnnotation\Validator;

use DateTime;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
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
        $this->errorMsg($errorMsg);
    }

    protected function validate(Param $param, ServerRequestInterface $request): bool
    {
        $itemData = $param->parsedValue();
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