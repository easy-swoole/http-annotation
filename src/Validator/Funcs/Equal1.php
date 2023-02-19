<?php

namespace EasySwoole\HttpAnnotation\Validator\Funcs;

use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\ValidateFuncInterface;

class Equal1 implements ValidateFuncInterface
{

    public function execute(AbstractValidator $validator)
    {
        $value = $validator->currentCheckParam()->parsedValue();
        return $value == 1;
    }

    public function errorMsg(): ?string
    {
        return null;
    }
}