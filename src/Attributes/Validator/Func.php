<?php

namespace EasySwoole\HttpAnnotation\Attributes\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use Psr\Http\Message\ServerRequestInterface;


class Func extends AbstractValidator
{

    protected $call;

    function __construct(callable $func,?string $errorMsg = null)
    {
        if(empty($errorMsg)){
            $errorMsg = "{#name} validate fail in custom function";
        }
        $this->errorMsg($errorMsg);
        $this->call = $func;
    }

    protected function validate(Param $param,ServerRequestInterface $request): bool
    {
        if($param->isOptional() && !$param->hasSet()){
            return true;
        }

        return (bool)call_user_func($this->call,$this);
    }

    function ruleName(): string
    {
        return "Func";
    }
}