<?php

namespace EasySwoole\HttpAnnotation\Attributes\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use Psr\Http\Message\ServerRequestInterface;

class Length extends AbstractValidator
{

    function __construct(public int $length,?string $errorMsg = null)
    {
        if(empty($errorMsg)){
            $errorMsg = "{#name} length must be {#length}";
        }
        $this->errorMsg($errorMsg);
    }


    protected function validate(Param $param, ServerRequestInterface $request): bool
    {
        $itemData = $param->parsedValue();
        if (is_numeric($itemData) || is_string($itemData)) {
            return strlen($itemData) == $this->length;
        }
        return false;
    }

    function ruleName(): string
    {
        return "Length";
    }
}