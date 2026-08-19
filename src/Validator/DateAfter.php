<?php

namespace EasySwoole\HttpAnnotation\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Exception\Annotation;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
use Psr\Http\Message\ServerRequestInterface;

class DateAfter extends AbstractValidator
{
    public $date;

    function __construct(string $date,string|null $errorMsg = null)
    {
        $this->date = $date;
        if(empty($errorMsg)){
            $errorMsg = "{#validateParam} must be date after {#date}";
        }
        $this->errorMsgTpl($errorMsg);
    }

    protected function validate(ValidateRequest $validateRequest): bool
    {
        $itemData = $validateRequest->validateParam->parsedValue();
        if (!is_string($itemData)) {
            return false;
        }

        if(is_numeric($this->date) && (strlen($this->date) == 10)){
            $afterUnixTime = $this->date;
        }else{
            $afterUnixTime = strtotime($this->date);
        }

        $unixTime = strtotime($itemData);

        if (is_bool($afterUnixTime)) {
            throw new Annotation("error arg:date for DateAfter validate rule");
        }

        if(is_bool($unixTime)){
            return false;
        }

        if ($unixTime > $afterUnixTime) {
            return true;
        }

        return false;
    }

    function ruleName(): string
    {
        return "DateAfter";
    }
}