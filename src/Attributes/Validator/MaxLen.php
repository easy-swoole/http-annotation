<?php

namespace EasySwoole\HttpAnnotation\Attributes\Validator;

use Psr\Http\Message\ServerRequestInterface;

#[\Attribute]
class MaxLen extends AbstractValidator
{
    protected $maxLen;

    function __construct(callable|int $maxLen,?string $errorMsg = null)
    {
        $this->maxLen = $maxLen;
        $this->errorMsg = $errorMsg;
    }

    function validate(ServerRequestInterface $request): bool
    {
        if($this->isIgnoreCheck()){
            return true;
        }
        if(is_callable($this->maxLen)){
            $compare = call_user_func($this->maxLen);
        }else{
            $compare = $this->maxLen;
        }
        $data = $this->param->parsedValue();
        if (is_numeric($data) || is_string($data)) {
            return strlen($data) <= $compare;
        }
        if (is_array($data) && (count($data) <= $compare)) {
            return true;
        }
        return  false;
    }
}