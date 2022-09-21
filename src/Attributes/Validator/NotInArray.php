<?php

namespace EasySwoole\HttpAnnotation\Attributes\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use Psr\Http\Message\ServerRequestInterface;

class NotInArray extends AbstractValidator
{

    public array $array;

    private bool $strict;

    function __construct(array $array,bool $strict = false,string $errorMsg = null)
    {
        $this->array = $array;
        $this->strict = $strict;
        if(empty($errorMsg)){
            $errorMsg = "{#name} must not in array of {#array}";
        }
        $this->errorMsg($errorMsg);
    }

    protected function validate(Param $param, ServerRequestInterface $request): bool
    {
        if($param->isOptional() && !$param->hasSet()){
            return true;
        }
        
        return !in_array($param->parsedValue(), $this->array, $this->strict);
    }

    function ruleName(): string
    {
        return "NotInArray";
    }
}