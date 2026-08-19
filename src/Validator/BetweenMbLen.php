<?php

namespace EasySwoole\HttpAnnotation\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
use Psr\Http\Message\ServerRequestInterface;

class BetweenMbLen extends AbstractValidator
{
    public int $minLen;
    public int $maxLen;

    function __construct(int $minLen,int $maxLen,string|null $errorMsg = null)
    {
        $this->minLen = $minLen;
        $this->maxLen = $maxLen;
        if(empty($errorMsg)){
            $errorMsg = "{#validateParam} length must between {#minLen} to {#maxLen}";
        }
        $this->errorMsgTpl($errorMsg);
    }

    protected function validate(ValidateRequest $validateRequest): bool
    {
        $data = $validateRequest->validateParam->parsedValue();
        if (!is_numeric($data) && !is_string($data)) {
            return false;
        }

        if (mb_strlen($data) >= $this->minLen && mb_strlen($data) <= $this->maxLen) {
            return true;
        }

        return  false;
    }

    function ruleName(): string
    {
        return "BetweenMbLen";
    }
}