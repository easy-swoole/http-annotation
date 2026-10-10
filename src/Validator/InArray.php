<?php

namespace EasySwoole\HttpAnnotation\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
use Psr\Http\Message\ServerRequestInterface;

class InArray extends AbstractValidator
{

    public array $array;

    protected bool $strict;

    function __construct(array $array,bool $strict = false,string|null $errorMsg = null)
    {
        $this->array = $array;
        $this->strict = $strict;
        if ($errorMsg !== null) {
            $this->errorMsgTpl($errorMsg);
        }
    }

    protected function validate(ValidateRequest $validateRequest): bool
    {
        return in_array($validateRequest->validateParam->parsedValue(), $this->array, $this->strict);
    }

    public static function ruleName(): string
    {
        return "InArray";
    }
}