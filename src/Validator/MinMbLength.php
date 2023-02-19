<?php

namespace EasySwoole\HttpAnnotation\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use Psr\Http\Message\ServerRequestInterface;


class MinMbLength extends AbstractValidator
{
    protected int $minLen;

    function __construct(int $minLen,?string $errorMsg = null)
    {
        $this->minLen = $minLen;
        if(empty($errorMsg)){
            $errorMsg = "{#name} min mb length is {#minLen}";
        }
        $this->errorMsg($errorMsg);
    }

    protected function validate(Param $param,ServerRequestInterface $request): bool
    {
        $itemData = $param->parsedValue();
        if (is_numeric($itemData) || is_string($itemData)) {
            return mb_strlen($itemData,mb_internal_encoding()) >= $this->minLen;
        }
        if (is_array($itemData) && (count($itemData) >= $this->minLen)) {
            return true;
        }
        return  false;
    }

    function ruleName(): string
    {
        return "MinMbLength";
    }
}