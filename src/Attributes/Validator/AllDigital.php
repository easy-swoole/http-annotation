<?php

namespace EasySwoole\HttpAnnotation\Attributes\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use Psr\Http\Message\ServerRequestInterface;


class AllDigital extends AbstractValidator
{
    function __construct(?string $errorMsg = null)
    {
        if(empty($errorMsg)){
            $errorMsg = "{#name} must be all digital";
        }
        $this->errorMsg($errorMsg);
    }


    protected function validate(Param $param, ServerRequestInterface $request): bool
    {
        if($param->isOptional() && !$param->hasSet()){
            return true;
        }

        return (bool)preg_match('/^\d+$/', (string)$this->currentCheckParam()->parsedValue());
    }

    function ruleName(): string
    {
        return "AllDigital";
    }
}