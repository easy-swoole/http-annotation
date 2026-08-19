<?php

namespace EasySwoole\HttpAnnotation\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Exception\Annotation;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
use Psr\Http\Message\ServerRequestInterface;

class TimestampAfter extends AbstractValidator
{
    public $compare;

    function __construct(string $compare, string|null $errorMsg = null)
    {
        if(empty($errorMsg)){
            $errorMsg = "{#validateParam} must be timestamp after {#compare}";
        }
        $this->errorMsgTpl($errorMsg);
        $this->compare = $compare;
    }

    protected function validate(ValidateRequest $validateRequest): bool
    {
        $itemData = $validateRequest->validateParam->parsedValue();
        if(!is_numeric($itemData)){
            return false;
        }

        $compare = $this->compare;
        if(!is_numeric($compare)){
            $compare = strtotime($compare);
            if(!$compare){
                throw new Annotation("error arg:compare for TimestampAfter validate rule");
            }
        }
        if($itemData > $compare){
            return true;
        }

        return false;
    }

    function ruleName(): string
    {
        return "TimestampAfter";
    }
}