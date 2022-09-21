<?php

namespace EasySwoole\HttpAnnotation\Attributes\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use Psr\Http\Message\ServerRequestInterface;


class MaxMbLength extends AbstractValidator
{
    protected int $maxLen;

    function __construct(int $maxLen,?string $errorMsg = null)
    {
        if(empty($errorMsg)){
            $errorMsg = "{#name} max mb Length is {#maxLen}";
        }
        $this->errorMsg($errorMsg);
        $this->maxLen = $maxLen;
    }

    protected function validate(Param $param,ServerRequestInterface $request): bool
    {
        if($param->isOptional() && !$param->hasSet()){
            return true;
        }

        $itemData = $param->parsedValue();
        if (is_numeric($itemData) || is_string($itemData)) {
            return mb_strlen($itemData,mb_internal_encoding()) <= $this->maxLen;
        }
        if (is_array($itemData) && (count($itemData) <= $this->maxLen)) {
            return true;
        }
        return  false;
    }

    function ruleName(): string
    {
        return "MaxMbLength";
    }
}