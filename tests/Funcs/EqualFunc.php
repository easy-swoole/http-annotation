<?php

namespace EasySwoole\HttpAnnotation\Tests\Funcs;

use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\ValidateFuncInterface;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;

class EqualFunc implements ValidateFuncInterface
{
    function __construct(
        private readonly int $equal
    ){}

    public function execute(ValidateRequest $validateRequest)
    {
        $value = $validateRequest->validateParam->parsedValue();
        return $value == $this->equal;
    }

    public function functionName(): string
    {
        return 'Equal';
    }


}