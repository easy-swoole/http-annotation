<?php

namespace EasySwoole\HttpAnnotation\Attributes\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Exception\Annotation;
use Psr\Http\Message\ServerRequestInterface;

class EqualWithColumn extends AbstractValidator
{

    public $compare;

    private bool $strict;

    function __construct(string $compare,bool $strict = false,string $errorMsg = null)
    {
        $this->compare = $compare;
        $this->strict = $strict;
        if(empty($errorMsg)){
            $errorMsg = "{#name} must equal with {#compare} column";
        }
        $this->errorMsg($errorMsg);
    }

    protected function validate(Param $param, ServerRequestInterface $request): bool
    {
        if($param->isOptional() && !$param->hasSet()){
            return true;
        }
        
        $itemData = $param->parsedValue();
        $list = $this->allCheckParams();
        if(!isset($list[$this->compare])){
            throw new Annotation("compare param: {$this->compare} require in DifferentWithColumn rule ,but not define in any controller annotation");
        }
        $compare = $list[$this->compare]->parsedValue();

        if($this->strict){
            return  $itemData === $compare;
        }else{
            return  $itemData == $compare;
        }
    }

    function ruleName(): string
    {
        return "EqualWithColumn";
    }
}