<?php

namespace EasySwoole\HttpAnnotation\Attributes\Validator;

use Psr\Http\Message\ServerRequestInterface;

#[\Attribute]
class MaxLen extends AbstractValidator
{
    protected int $maxLen;

    function __construct(int $maxLen,?string $errorMsg = null)
    {
        $this->maxLen = $maxLen;
        $this->errorMsg = $errorMsg;
    }

    function validate(ServerRequestInterface $request): bool
    {
        if($this->isIgnoreCheck()){
            return true;
        }
        $data = $this->param->parsedValue();
        if (is_numeric($data) || is_string($data)) {
            return strlen($data) <= $this->maxLen;
        }
        if (is_array($data) && (count($data) <= $this->maxLen)) {
            return true;
        }
        return  false;
    }
}