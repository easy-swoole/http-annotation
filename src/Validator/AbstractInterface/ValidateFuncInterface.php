<?php

namespace EasySwoole\HttpAnnotation\Validator\AbstractInterface;

interface ValidateFuncInterface
{
    public function execute(AbstractValidator $validator);
    public function functionName():string;
}