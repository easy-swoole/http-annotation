<?php

namespace EasySwoole\HttpAnnotation\Attributes\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
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
        $compare = $this->minLen;
        $data = $param->parsedValue();
        if (is_numeric($data) || is_string($data)) {
            return mb_strlen($data,mb_internal_encoding()) >= $compare;
        }
        return  false;
    }

    function ruleName(): string
    {
        return "MinMbLength";
    }
}