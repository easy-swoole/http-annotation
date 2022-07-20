<?php

namespace EasySwoole\HttpAnnotation\Attributes\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use Psr\Http\Message\ServerRequestInterface;


class Regex extends AbstractValidator
{

    protected $rule;

    function __construct(string $rule,?string $errorMsg = null)
    {
        if(empty($errorMsg)){
            $errorMsg = "{#name} must meet specified rules";
        }
        $this->errorMsg($errorMsg);
        $this->rule = $rule;
    }

    protected function validate(Param $param,ServerRequestInterface $request): bool
    {
        $itemData = $param->parsedValue();
        if (!is_numeric($itemData) && !is_string($itemData)) {
            return false;
        }

        return (bool)preg_match($this->rule, (string)$itemData);
    }

    function ruleName(): string
    {
        return "Func";
    }
}