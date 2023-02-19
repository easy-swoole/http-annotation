<?php

namespace EasySwoole\HttpAnnotation\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use Psr\Http\Message\ServerRequestInterface;

class Min extends AbstractValidator
{
    protected int|float $min;

    function __construct(int|float $min,?string $errorMsg = null)
    {
        $this->min = $min;
        if(empty($errorMsg)){
            $errorMsg = "{#name} min value is {#min}";
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
        if($data < $this->min){
            return false;
        }
        return true;
    }

    function ruleName(): string
    {
        return "min";
    }
}