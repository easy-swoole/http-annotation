<?php

namespace EasySwoole\HttpAnnotation\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use Psr\Http\Message\ServerRequestInterface;

class IsPhoneNumber extends AbstractValidator
{

    function __construct(?string $errorMsg = null)
    {
        if(empty($errorMsg)){
            $errorMsg = "{#name} must be phone number";
        }
        $this->errorMsg($errorMsg);
    }
    protected function validate(Param $param, ServerRequestInterface $request): bool
    {
        return (bool)preg_match( '/^1[3456789]\d{9}$/', (string)$param->parsedValue());
    }

    function ruleName(): string
    {
        return 'IsPhoneNumber';
    }
}