<?php

namespace EasySwoole\HttpAnnotation\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use Psr\Http\Message\ServerRequestInterface;

class Different extends AbstractValidator
{
    public $compare;
    private bool $strict;

    function __construct(string|float|int $compare,bool $strict = false,string $errorMsg = null)
    {
        $this->compare = $compare;
        $this->strict = $strict;
        if(empty($errorMsg)){
            $errorMsg = "{#name} must different with {#compare}";
        }
        $this->errorMsg($errorMsg);
    }

    protected function validate(Param $param, ServerRequestInterface $request): bool
    {
        $itemData = $param->parsedValue();
        return !($this->strict ? $itemData === $this->compare : $itemData == $this->compare);
    }

    function ruleName(): string
    {
        return "Different";
    }
}