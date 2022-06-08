<?php

namespace EasySwoole\HttpAnnotation\Attributes\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use Psr\Http\Message\ServerRequestInterface;

#[\Attribute]
class MaxLen extends AbstractValidator
{
    protected $maxLen;

    function __construct(callable|int $maxLen,?string $errorMsg = null)
    {
        $this->maxLen = $maxLen;
        $this->errorMsg($errorMsg);
    }

    protected function validate(Param $param,ServerRequestInterface $request): bool
    {
        if($this->isIgnoreCheck($param)){
            return true;
        }
        if(is_callable($this->maxLen)){
            $compare = call_user_func($this->maxLen,$param,$request,$this);
        }else{
            $compare = $this->maxLen;
        }
        $data = $param->parsedValue();
        if (is_numeric($data) || is_string($data)) {
            return strlen($data) <= $compare;
        }
        if (is_array($data) && (count($data) <= $compare)) {
            return true;
        }
        return  false;
    }

    function ruleName(): string
    {
        return "MaxLen";
    }
}