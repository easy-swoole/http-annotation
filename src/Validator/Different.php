<?php

namespace EasySwoole\HttpAnnotation\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
use Psr\Http\Message\ServerRequestInterface;

class Different extends AbstractValidator
{
    public mixed $compare;
    private bool $strict;

    function __construct(string|float|int $compare,bool $strict = false,string|null $errorMsg = null)
    {
        $this->compare = $compare;
        $this->strict = $strict;
        if(empty($errorMsg)){
            $errorMsg = "{#validateParam} must different with {#compare}";
        }
        $this->errorMsgTpl($errorMsg);
    }

    protected function validate(ValidateRequest $validateRequest): bool
    {
        $itemData = $validateRequest->validateParam->parsedValue();
        return !($this->strict ? $itemData === $this->compare : $itemData == $this->compare);
    }

    function ruleName(): string
    {
        return "Different";
    }
}