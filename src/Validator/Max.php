<?php

namespace EasySwoole\HttpAnnotation\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use Psr\Http\Message\ServerRequestInterface;

class Max extends AbstractValidator
{
    protected int|float $max;

    function __construct(int|float $max,?string $errorMsg = null)
    {
        $this->max = $max;
        if(empty($errorMsg)){
            $errorMsg = "{#name} max value is {#max}";
        }
        $this->errorMsg($errorMsg);
    }

    protected function validate(Param $param, ServerRequestInterface $request): bool
    {
        $data = $param->parsedValue();
        if(!is_numeric($data)){
            return false;
        }
        $data = $data * 1;
        if($data > $this->max){
            return false;
        }
        return true;
    }

    function ruleName(): string
    {
        return "Max";
    }
}