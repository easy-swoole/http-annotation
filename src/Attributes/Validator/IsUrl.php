<?php

namespace EasySwoole\HttpAnnotation\Attributes\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use Psr\Http\Message\ServerRequestInterface;

class IsUrl extends AbstractValidator
{
    function __construct(?string $errorMsg = null)
    {
        if(empty($errorMsg)){
            $errorMsg = "{#name} must be a url";
        }
        $this->errorMsg($errorMsg);
    }

    protected function validate(Param $param, ServerRequestInterface $request): bool
    {
        $itemData = $param->parsedValue();
        if (!is_string($itemData)) {
            return false;
        }

        if (!filter_var($itemData, FILTER_VALIDATE_URL)) {
            return false;
        }
        return true;
    }

    function ruleName(): string
    {
        return 'IsUrl';
    }
}