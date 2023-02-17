<?php

namespace EasySwoole\HttpAnnotation\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use Psr\Http\Message\ServerRequestInterface;

class TimestampAfterDate extends AbstractValidator
{
    public $date;

    function __construct(string $date,?string $errorMsg = null)
    {
        if(empty($errorMsg)){
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