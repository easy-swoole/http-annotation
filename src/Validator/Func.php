<?php

namespace EasySwoole\HttpAnnotation\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\ValidateFuncInterface;
use Psr\Http\Message\ServerRequestInterface;


class Func extends AbstractValidator
{

    protected $call;

    function __construct(ValidateFuncInterface|callable $func,?string $errorMsg = null)
    {
        if(empty($errorMsg)){
            if($func instanceof ValidateFuncInterface){
                $errorMsg = "{#name} validate fail in {$func->functionName()} function";
            }else{
                $errorMsg = "{#name} validate fail in custom function";
            }
        }
        $this->errorMsg($errorMsg);
        $this->call = $func;
    }

    protected function validate(Param $param,ServerRequestInterface $request): bool
    {
        if($this->call instanceof ValidateFuncInterface){
            return $this->call->execute($this);
        }
        return (bool)call_user_func($this->call,$this);
    }

    function ruleName(): string
    {
        return "Func";
    }

    function __clone(): void
    {
        if($this->call instanceof ValidateFuncInterface){
            $this->call = clone $this->call;
        }
    }
}