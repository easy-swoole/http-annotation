<?php

namespace EasySwoole\HttpAnnotation\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use Psr\Http\Message\ServerRequestInterface;

class Between extends AbstractValidator
{
    protected $min;
    protected $max;

    function __construct(float|int|string $min,float|int|string $max,?string $errorMsg = null)
    {
        $this->min = $min;
        $this->max = $max;
        if(empty($errorMsg)){
            $errorMsg = "{#name} must between {#min} to {#max}";
        }
        $this->errorMsg($errorMsg);
    }

    protected function validate(Param $param, ServerRequestInterface $request): bool
    {
        $data = $param->parsedValue();
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