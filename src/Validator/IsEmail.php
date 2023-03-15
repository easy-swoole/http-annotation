<?php

namespace EasySwoole\HttpAnnotation\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use Psr\Http\Message\ServerRequestInterface;

class IsEmail extends AbstractValidator
{
    function __construct(?string $errorMsg = null)
    {
        if(empty($errorMsg)){
            $errorMsg = "{#name} must be a email address";
        }
        $this->errorMsg($errorMsg);
    }


    protected function validate(Param $param, ServerRequestInterface $request): bool
    {
        $itemData = $param->parsedValue();
        if (!is_string($itemData)) {
            return false;
        }

        if (!filter_var($itemData, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        return true;
    }

    function ruleName(): string
    {
        return 'IsEmail';
    }
}