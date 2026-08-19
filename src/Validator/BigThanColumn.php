<?php

namespace EasySwoole\HttpAnnotation\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Exception\Annotation;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
use Psr\Http\Message\ServerRequestInterface;

class BigThanColumn extends AbstractValidator
{

    function __construct(public string $paramName,string|null $errorMsg = null)
    {
        if(empty($errorMsg)){
            $errorMsg = "{#validateParam} value must big than {$paramName} value";
        }
        $this->errorMsgTpl($errorMsg);
    }

    protected function validate(ValidateRequest $validateRequest): bool
    {
        $itemData = $validateRequest->validateParam->parsedValue();
        $list = $validateRequest->allDefineParams;
        if(!isset($list[$this->paramName])){
            throw new Annotation("compare param: {$this->paramName} require in BigThanColumn rule ,but not define in any controller annotation");
        }
        $compare = $list[$this->paramName]->parsedValue();
        if($itemData > $compare){
            return true;
        }else{
            return false;
        }
    }

    function ruleName(): string
    {
        return 'BigThanColumn';
    }
}