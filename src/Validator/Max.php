<?php

namespace EasySwoole\HttpAnnotation\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
use Psr\Http\Message\ServerRequestInterface;

class Max extends AbstractValidator
{
    protected int|float $max;

    function __construct(int|float $max,string|null $errorMsg = null)
    {
        $this->max = $max;
        if(empty($errorMsg)){
            $errorMsg = "{#name} max value is {#max}";
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