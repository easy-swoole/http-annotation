<?php

namespace EasySwoole\HttpAnnotation\Attributes\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use Psr\Http\Message\ServerRequestInterface;

class Between extends AbstractValidator
{
    protected $min;
    protected $max;

    function __construct(callable|int $min,callable|int $max,?string $errorMsg = null)
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
        if($param->isOptional() && !$param->hasSet()){
            return true;
        }
        
        $data = $param->parsedValue();
        if (!is_numeric($data) && !is_string($data)) {
            return false;
        }
        if(is_callable($this->min)){
            $this->min = call_user_func($this->min,$this);
        }

        if(is_callable($this->max)){
            $this->max = call_user_func($this->max,$this);
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