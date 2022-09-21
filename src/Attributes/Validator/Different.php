<?php

namespace EasySwoole\HttpAnnotation\Attributes\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use Psr\Http\Message\ServerRequestInterface;

class Different extends AbstractValidator
{
    public $compare;
    private bool $strict;

    function __construct($compare,bool $strict = false,string $errorMsg = null)
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
        if($param->isOptional() && !$param->hasSet()){
            return true;
        }
        
        if(is_callable($this->compare)){
            $this->compare = call_user_func($this->compare);
        }
        $itemData = $param->parsedValue();
        return !($this->strict ? $itemData === $this->compare : $itemData == $this->compare);
    }

    function ruleName(): string
    {
        return "Different";
    }
}