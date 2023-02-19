<?php

namespace EasySwoole\HttpAnnotation\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use Psr\Http\Message\ServerRequestInterface;

class Length extends AbstractValidator
{
    protected int $length;
    function __construct(int $length,?string $errorMsg = null)
    {
        if(empty($errorMsg)){
            $errorMsg = "{#name} length must be {#length}";
        }
        $this->errorMsg($errorMsg);
        $this->length = $length;
    }


    protected function validate(Param $param, ServerRequestInterface $request): bool
    {
        $itemData = $param->parsedValue();
        if (is_numeric($itemData) || is_string($itemData)) {
            return strlen($itemData) == $this->length;
        }
        if (is_array($itemData) && (count($itemData) == $this->length)) {
            return true;
        }
        return false;
    }

    function ruleName(): string
    {
        return "Length";
    }
}