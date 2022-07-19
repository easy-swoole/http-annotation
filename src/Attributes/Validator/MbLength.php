<?php

namespace EasySwoole\HttpAnnotation\Attributes\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use Psr\Http\Message\ServerRequestInterface;

class MbLength extends AbstractValidator
{

    function __construct(public int $length,?string $errorMsg = null)
    {
        if(empty($errorMsg)){
            $errorMsg = "{#name} mb length must be {#length}";
        }
        $this->errorMsg($errorMsg);
    }


    protected function validate(Param $param, ServerRequestInterface $request): bool
    {
        $itemData = $param->parsedValue();
        if (is_numeric($itemData) || is_string($itemData)) {
            return mb_strlen($itemData,mb_internal_encoding()) == $this->length;
        }
        return false;
    }

    function ruleName(): string
    {
        return "MbLength";
    }
}