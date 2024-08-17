<?php

namespace EasySwoole\HttpAnnotation\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Exception\Annotation;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use Psr\Http\Message\ServerRequestInterface;

class SmallThanColumn extends AbstractValidator
{

    function __construct(public string $paramName,?string $errorMsg = null)
    {
        if(empty($errorMsg)){
            $errorMsg = "{#name} value must small than {$paramName} value";
        }
        $this->errorMsg($errorMsg);
    }

    protected function validate(Param $param, ServerRequestInterface $request): bool
    {
        $itemData = $param->parsedValue();
        $list = $this->allCheckParams();
        if(!isset($list[$this->paramName])){
            throw new Annotation("compare param: {$this->paramName} require in SmallThanColumn rule ,but not define in any controller annotation");
        }
        $compare = $list[$this->paramName]->parsedValue();
        if($itemData > $compare){
            return false;
        }else{
            return true;
        }
    }

    function ruleName(): string
    {
        return 'SmallThanColumn';
    }
}