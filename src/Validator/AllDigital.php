<?php

namespace EasySwoole\HttpAnnotation\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
use Psr\Http\Message\ServerRequestInterface;


class AllDigital extends AbstractValidator
{
    function __construct(string|null $errorMsg = null)
    {
        if($errorMsg !== null){
            $this->errorMsgTpl($errorMsg);
        }
    }


    protected function validate(ValidateRequest $validateRequest): bool
    {
        return (bool)preg_match('/^\d+$/', (string)$validateRequest->validateParam->parsedValue());
    }

    public static function ruleName(): string
    {
        return "AllDigital";
    }
}