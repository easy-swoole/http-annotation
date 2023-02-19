<?php

namespace EasySwoole\HttpAnnotation\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use Psr\Http\Message\ServerRequestInterface;

class InArray extends AbstractValidator
{

    public array $array;

    private bool $strict;

    function __construct(array $array,bool $strict = false,string $errorMsg = null)
    {
        $this->array = $array;
        $this->strict = $strict;
        if(empty($errorMsg)){
            $errorMsg = "{#name} must in array of {#array}";
        }
        $this->errorMsg($errorMsg);
    }

    protected function validate(Param $param, ServerRequestInterface $request): bool
    {
        return in_array($param->parsedValue(), $this->array, $this->strict);
    }

    function ruleName(): string
    {
        return "InArray";
    }
}