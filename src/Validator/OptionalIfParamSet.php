<?php

namespace EasySwoole\HttpAnnotation\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use Psr\Http\Message\ServerRequestInterface;

class OptionalIfParamSet extends AbstractValidator
{

    protected string $paramName;
    function __construct(string $paramName,?string $errorMsg = null)
    {
        $this->paramName = $paramName;
        if(empty($errorMsg)){
            $errorMsg = "{#name} is optional when param {$paramName} set";
        }
        $this->errorMsg($errorMsg);
    }

    protected function validate(Param $param, ServerRequestInterface $request): bool
    {
        return true;
    }

    function ruleName(): string
    {
        return 'OptionalIfParamSet';
    }
}