<?php

namespace EasySwoole\HttpAnnotation\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
use Psr\Http\Message\ServerRequestInterface;

class Min extends AbstractValidator
{
    protected int|float $min;

    function __construct(int|float $min,string|null $errorMsg = null)
    {
        $this->min = $min;
        if(empty($errorMsg)){
            $errorMsg = "{#validateParam} min value is {#min}";
        }
        $this->errorMsgTpl($errorMsg);
    }

    protected function validate(ValidateRequest $validateRequest): bool
    {
        $data = $validateRequest->validateParam->parsedValue();
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