<?php

namespace EasySwoole\HttpAnnotation\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use Psr\Http\Message\ServerRequestInterface;

class MbLength extends AbstractValidator
{
    protected int $length;
    function __construct(int $length,?string $errorMsg = null)
    {
        if(empty($errorMsg)){
            $errorMsg = "{#name} mb length must be {#length}";
        }
        $this->errorMsg($errorMsg);
        $this->length = $length;
    }


    protected function validate(Param $param, ServerRequestInterface $request): bool
    {
        $itemData = $param->parsedValue();
        if (is_numeric($itemData) || is_string($itemData)) {
            return mb_strlen($itemData,mb_internal_encoding()) == $this->length;
        }
        if (is_array($itemData) && (count($itemData) == $this->length)) {
            return true;
        }
        return false;
    }

    function ruleName(): string
    {
        return "MbLength";
    }
}