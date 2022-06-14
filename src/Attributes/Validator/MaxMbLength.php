<?php

namespace EasySwoole\HttpAnnotation\Attributes\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use Psr\Http\Message\ServerRequestInterface;


class MaxMbLength extends AbstractValidator
{
    protected int $maxLen;

    function __construct(int $maxLen,?string $errorMsg = null)
    {
        $this->maxLen = $maxLen;
        if(empty($errorMsg)){
            $errorMsg = "{#name} max mb Length is {#maxLen}";
        }
        $this->errorMsg($errorMsg);
    }

    protected function validate(Param $param,ServerRequestInterface $request): bool
    {
        $compare = $this->maxLen;
        $data = $param->parsedValue();
        if (is_numeric($data) || is_string($data)) {
            return mb_strlen($data,mb_internal_encoding()) <= $compare;
        }
        return  false;
    }

    function ruleName(): string
    {
        return "MaxMbLength";
    }
}