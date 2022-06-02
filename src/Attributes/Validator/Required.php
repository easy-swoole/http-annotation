<?php

namespace EasySwoole\HttpAnnotation\Attributes\Validator;

use Psr\Http\Message\ServerRequestInterface;

#[\Attribute]
class Required extends AbstractValidator
{
    function __construct(?string $errorMsg = null)
    {
        $this->errorMsg = $errorMsg;
    }

    function validate(ServerRequestInterface $request): bool
    {
        if($this->isIgnoreCheck()){
            return true;
        }
        if((!$this->param->isNullData()) && ($this->param->parsedValue() === null)){
            return false;
        }
        return true;
    }

}