<?php

namespace EasySwoole\HttpAnnotation\Attributes\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Exception\Annotation;
use Psr\Http\Message\ServerRequestInterface;

class Timestamp extends AbstractValidator
{

    function __construct(?string $errorMsg = null)
    {
        if(empty($errorMsg)){
            $errorMsg = "{#name} must be timestamp";
        }
        $this->errorMsg($errorMsg);
    }

    protected function validate(Param $param, ServerRequestInterface $request): bool
    {
        if($param->isOptional() && !$param->hasSet()){
            return true;
        }
        
        $itemData = $param->parsedValue();
        if (!is_numeric($itemData)) {
            return false;
        }

        if (strtotime(date('d-m-Y H:i:s', $itemData)) === (int)$itemData) {
            return true;
        }

        return false;
    }

    function ruleName(): string
    {
        return "Timestamp";
    }
}