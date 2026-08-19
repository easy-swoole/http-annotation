<?php

namespace EasySwoole\HttpAnnotation\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
use Psr\Http\Message\ServerRequestInterface;

class NotInArray extends AbstractValidator
{

    public array $array;

    private bool $strict;

    function __construct(array $array,bool $strict = false,string|null $errorMsg = null)
    {
        $this->array = $array;
        $this->strict = $strict;
        if(empty($errorMsg)){
            $errorMsg = "{#name} must not in array of {#array}";
        }
        $this->errorMsgTpl($errorMsg);
    }

    protected function validate(ValidateRequest $validateRequest): bool
    {
        return !in_array($validateRequest->validateParam->parsedValue(), $this->array, $this->strict);
    }

    function ruleName(): string
    {
        return "NotInArray";
    }
}