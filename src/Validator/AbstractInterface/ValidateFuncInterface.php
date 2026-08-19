<?php

namespace EasySwoole\HttpAnnotation\Validator\AbstractInterface;

use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;

interface ValidateFuncInterface
{
    public function execute(ValidateRequest $validateRequest);
    public function functionName():string;
}