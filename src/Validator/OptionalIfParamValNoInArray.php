<?php

namespace EasySwoole\HttpAnnotation\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
use Psr\Http\Message\ServerRequestInterface;

class OptionalIfParamValNoInArray extends AbstractValidator
{

    protected string $paramName;
    protected array $inVal;

    function __construct(string $paramName,array $inVal,string|null $errorMsg = null)
    {
        $this->inVal = $inVal;
        $this->paramName = $paramName;
        if ($errorMsg !== null) {
            $this->errorMsgTpl($errorMsg);
        }
    }
    protected function validate(ValidateRequest $validateRequest): bool
    {
        return true;
    }

    public static function ruleName(): string
    {
        return 'OptionalIfParamValNoInArray';
    }
}