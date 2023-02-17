<?php

namespace EasySwoole\HttpAnnotation\Attributes\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Exception\Annotation;
use Psr\Http\Message\ServerRequestInterface;

class TimestampBefore extends AbstractValidator
{
    public $date;

    function __construct(string|callable $date,?string $errorMsg = null)
    {
        if(empty($errorMsg)){
            $errorMsg = "{#name} must be timestamp before {#date}";
        }
        $this->errorMsg($errorMsg);
        $this->date = $date;
    }

    protected function validate(Param $param, ServerRequestInterface $request): bool
    {
        $itemData = $param->parsedValue();

        if(is_callable($this->date)){
            $this->date = call_user_func($this->date,$this);
        }

        if (is_numeric($itemData) && is_numeric($this->date)) {
            return intval($itemData) < intval($this->date);
        }

        return false;
    }

    function ruleName(): string
    {
        return "TimestampBefore";
    }
}