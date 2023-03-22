<?php

namespace EasySwoole\HttpAnnotation\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use Psr\Http\Message\ServerRequestInterface;

class OptionalIfParamValInArray extends AbstractValidator
{
    protected string $paramName;
    protected array $inVal;

    function __construct(string $paramName,array $inVal,?string $errorMsg = null)
    {
        $this->inVal = $inVal;
        $this->paramName = $paramName;
        if(empty($errorMsg)){
            $errorMsg = "{#name} is optional when param {$paramName} value is in ".json_encode($inVal);
        }
        $this->errorMsg($errorMsg);
    }
    protected function validate(Param $param, ServerRequestInterface $request): bool
    {
        return true;
    }

    function ruleName(): string
    {
        return 'OptionalIfParamValInArray';
    }

}