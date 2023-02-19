<?php

namespace EasySwoole\HttpAnnotation\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use Psr\Http\Message\ServerRequestInterface;


class MaxLength extends AbstractValidator
{
    protected int $maxLen;

    function __construct(int $maxLen,?string $errorMsg = null)
    {
        if(empty($errorMsg)){
            $errorMsg = "{#name} max length is {#maxLen}";
        }
        $this->errorMsg($errorMsg);
        $this->maxLen = $maxLen;
    }

    protected function validate(Param $param,ServerRequestInterface $request): bool
    {
        $itemData = $param->parsedValue();
        if (is_numeric($itemData) || is_string($itemData)) {
            return strlen($itemData) <= $this->maxLen;
        }
        if (is_array($itemData) && (count($itemData) <= $this->maxLen)) {
            return true;
        }
        return  false;
    }

    function ruleName(): string
    {
        return "MaxLength";
    }
}