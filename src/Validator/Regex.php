<?php

namespace EasySwoole\HttpAnnotation\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
use Psr\Http\Message\ServerRequestInterface;


class Regex extends AbstractValidator
{

    protected $rule;

    function __construct(string $rule,string|null $errorMsg = null)
    {
        if(empty($errorMsg)){
            $errorMsg = "{#validateParam} must meet specified rule: {$rule}";
        }
        $this->errorMsgTpl($errorMsg);
        $this->rule = $rule;
    }

    protected function validate(ValidateRequest $validateRequest): bool
    {
        $itemData = $validateRequest->validateParam->parsedValue();
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