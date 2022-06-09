<?php

namespace EasySwoole\HttpAnnotation\Attributes\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use Psr\Http\Message\ServerRequestInterface;

class BetweenLen extends AbstractValidator
{
    public $minLen;
    public $maxLen;

    function __construct(callable|int $minLen,callable|int $maxLen,?string $errorMsg = null)
    {
        $this->minLen = $minLen;
        $this->maxLen = $maxLen;
        if(empty($errorMsg)){
            $errorMsg = "{#name} length must between {#minLen} to {#maxLen}";
        }
        $this->errorMsg($errorMsg);
    }

    protected function validate(Param $param, ServerRequestInterface $request): bool
    {
        $data = $param->parsedValue();

        if (!is_numeric($data) && !is_string($data)) {
            return false;
        }
        if(is_callable($this->minLen)){
            $this->minLen = call_user_func($this->minLen,$this);
        }

        if(is_callable($this->maxLen)){
            $this->maxLen = call_user_func($this->maxLen,$this);
        }

        if (strlen($data) >= $this->minLen && strlen($data) <= $this->maxLen) {
            return true;
        }

        return false;
    }

    function ruleName(): string
    {
        return "BetweenLen";
    }
}