<?php

namespace EasySwoole\HttpAnnotation\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\ValidateFuncInterface;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
use Psr\Http\Message\ServerRequestInterface;


class Func extends AbstractValidator
{

    protected $call;

    function __construct(ValidateFuncInterface|callable $func,string|null $errorMsg = null)
    {
        if(empty($errorMsg)){
            if($func instanceof ValidateFuncInterface){
                $errorMsg = "{#name} validate fail in {$func->functionName()} function";
            }else{
                $errorMsg = "{#name} validate fail in custom function";
            }
        }
        $this->errorMsgTpl($errorMsg);
        $this->call = $func;
    }

    protected function validate(ValidateRequest $validateRequest): bool
    {
        if($this->call instanceof ValidateFuncInterface){
            return $this->call->execute($validateRequest);
        }
        return (bool)call_user_func($this->call,$validateRequest);
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