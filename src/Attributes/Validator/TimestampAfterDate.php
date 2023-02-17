<?php

namespace EasySwoole\HttpAnnotation\Attributes\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Exception\Annotation;
use Psr\Http\Message\ServerRequestInterface;

class TimestampAfterDate extends AbstractValidator
{
    public $date;

    function __construct(string|callable $date,?string $errorMsg = null)
    {
        if(empty($errorMsg)){
            if(is_callable($date)){
                $date = call_user_func($date,$this);
            }
            $errorMsg = "{#name} must be timestamp after {#date}";
        }
        $this->errorMsg($errorMsg);
        $this->date = $date;
    }

    protected function validate(Param $param, ServerRequestInterface $request): bool
    {
        $itemData = $param->parsedValue();
        if (!is_numeric($itemData)) {
            return false;
        }

        if(is_callable($this->date)){
            $this->date = call_user_func($this->date,$this);
        }
        $time = strtotime($this->date);

        if ($time !== false && $time > 0 && $time < $itemData) {
            return true;
        }

        return false;
    }

    function ruleName(): string
    {
        return "TimestampAfterDate";
    }
}