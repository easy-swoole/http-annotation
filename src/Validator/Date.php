<?php

namespace EasySwoole\HttpAnnotation\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Exception\Annotation;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
use Psr\Http\Message\ServerRequestInterface;

class Date extends AbstractValidator
{
    public $date;
    function __construct(string $date,string|null $errorMsg = null)
    {
        $this->date = $date;
        if(empty($errorMsg)){
            $errorMsg = "{#validateParam} must be date {#date}";
        }
        $this->errorMsgTpl($errorMsg);
    }

    protected function validate(ValidateRequest $validateRequest): bool
    {
        $itemData = $validateRequest->validateParam->parsedValue();
        if (!is_string($itemData)) {
            return false;
        }

        $unixTime = strtotime($itemData);
        if(is_bool($unixTime)){
            return false;
        }
        $compare = strtotime($this->date);

        if (is_bool($compare)) {
            throw new Annotation("error arg:date for Date validate rule");
        }

        if(date("Y-m-d",$compare) == date("Y-m-d",$unixTime)){
            return true;
        }
        return false;
    }

    function ruleName(): string
    {
        return 'Date';
    }
}