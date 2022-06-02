<?php

namespace EasySwoole\HttpAnnotation\Attributes\Validator;

use Psr\Http\Message\ServerRequestInterface;

#[\Attribute]
class Func extends AbstractValidator
{

    protected $call;

    function __construct(callable $func,?string $errorMsg = null)
    {
        $this->call = $func;
        $this->errorMsg = $errorMsg;
    }

    function validate(ServerRequestInterface $request): bool
    {
        return call_user_func($this->call,$this->param,$request,$this->api);
    }
}