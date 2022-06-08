<?php

namespace EasySwoole\HttpAnnotation\Attributes\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use Psr\Http\Message\ServerRequestInterface;

#[\Attribute]
class MaxLen extends AbstractValidator
{
    protected int $maxLen;

    function __construct(int $maxLen,?string $errorMsg = null)
    {
        $this->maxLen = $maxLen;
        $this->errorMsg($errorMsg);
    }

    protected function validate(Param $param,ServerRequestInterface $request): bool
    {
        $compare = $this->maxLen;
        $data = $param->parsedValue();
        if (is_numeric($data) || is_string($data)) {
            return strlen($data) <= $compare;
        }
        if (is_array($data) && (count($data) <= $compare)) {
            return true;
        }
        return  false;
    }

    function ruleName(): string
    {
        return "MaxLen";
    }
}