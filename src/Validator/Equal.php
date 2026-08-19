<?php

namespace EasySwoole\HttpAnnotation\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
use Psr\Http\Message\ServerRequestInterface;

class Equal extends AbstractValidator
{

    private bool $strict;
    public mixed $compare;

    function __construct(string|int|null|float $compare,bool $strict = false,string|null $errorMsg = null)
    {
        $this->compare = $compare;
        $this->strict = $strict;
        if(empty($errorMsg)){
            $errorMsg = "{#name} must equal with {#compare}";
        }
        $this->errorMsgTpl($errorMsg);
    }


    protected function validate(ValidateRequest $validateRequest): bool
    {
        $itemData = $validateRequest->validateParam->parsedValue();
        return ($this->strict ? $itemData === $this->compare : $itemData == $this->compare);
    }

    function ruleName(): string
    {
        return "Equal";
    }
}