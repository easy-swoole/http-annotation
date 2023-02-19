<?php

namespace EasySwoole\HttpAnnotation\Validator\Funcs;

use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\ValidateFuncInterface;

class EqualFunc implements ValidateFuncInterface
{
    function __construct(
        private readonly int $equal
    )
    {

    }

    public function execute(AbstractValidator $validator)
    {
        $value = $validator->currentCheckParam()->parsedValue();
        return $value == $this->equal;
    }

    public function functionName(): string
    {
        return 'Equal1';
    }


}