<?php

namespace EasySwoole\HttpAnnotation\Attributes\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Exception\Annotation;
use Psr\Http\Message\ServerRequestInterface;

class DateBefore extends AbstractValidator
{

    public $date;

    function __construct(string|callable $date,?string $errorMsg = null)
    {
        $this->date = $date;
        if(empty($errorMsg)){
            $errorMsg = "{#name} must be date before {#date}";
        }
        $this->errorMsg($errorMsg);
    }

    protected function validate(Param $param, ServerRequestInterface $request): bool
    {
        $itemData = $param->parsedValue();
        if (!is_string($itemData)) {
            return false;
        }

        if(is_callable($this->date)){
            $this->date = call_user_func($this->date,$this);
        }

        if(is_numeric($this->date) && (strlen($this->date) == 10)){
            $beforeUnixTime = $this->date;
        }else{
            if($this->date == 'today'){
                $beforeUnixTime = strtotime(date("Y-m-d"));
            }else{
                $beforeUnixTime = strtotime($this->date);
            }
        }


        $unixTime = strtotime($itemData);

        if (is_bool($beforeUnixTime)) {
            throw new Annotation("error arg:date for DateAfter validate rule");
        }

        if(is_bool($unixTime)){
            return false;
        }

        if ($unixTime < $beforeUnixTime) {
            return true;
        }

        return false;
    }

    function ruleName(): string
    {
        return "DateBefore";
    }
}