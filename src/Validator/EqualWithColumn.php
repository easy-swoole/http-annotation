<?php

namespace EasySwoole\HttpAnnotation\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Exception\Annotation;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
use Psr\Http\Message\ServerRequestInterface;

class EqualWithColumn extends AbstractValidator
{

    public $compare;

    private bool $strict;

    function __construct(string $compare,bool $strict = false,string|null $errorMsg = null)
    {
        $this->compare = $compare;
        $this->strict = $strict;
        if(empty($errorMsg)){
            $errorMsg = "{#name} must equal with {#compare} column";
        }
        $this->errorMsgTpl($errorMsg);
    }

    protected function validate(ValidateRequest $validateRequest): bool
    {
        $itemData = $validateRequest->validateParam->parsedValue();
        $list = $validateRequest->allDefineParams;
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