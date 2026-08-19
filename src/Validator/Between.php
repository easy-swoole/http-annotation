<?php

namespace EasySwoole\HttpAnnotation\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
use Psr\Http\Message\ServerRequestInterface;

class Between extends AbstractValidator
{
    protected float|int $min;
    protected float|int $max;

    function __construct(float|int $min,float|int $max,string|null $errorMsg = null)
    {
        $this->min = $min;
        $this->max = $max;
        if(empty($errorMsg)){
            $errorMsg = "{#name} must between {#min} to {#max}";
        }
        $this->errorMsgTpl($errorMsg);
    }

    protected function validate(ValidateRequest $validateRequest): bool
    {
        $data = $validateRequest->validateParam->parsedValue();
        if (!is_numeric($data) && !is_string($data)) {
            return false;
        }

        if ($data <= $this->max && $data >= $this->min) {
            return true;
        }

        return false;
    }

    function ruleName(): string
    {
        return "Between";
    }
}