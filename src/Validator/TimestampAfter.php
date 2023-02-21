<?php

namespace EasySwoole\HttpAnnotation\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Exception\Annotation;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use Psr\Http\Message\ServerRequestInterface;

class TimestampAfter extends AbstractValidator
{
    public $compare;

    function __construct(string $compare, ?string $errorMsg = null)
    {
        if(empty($errorMsg)){
            $errorMsg = "{#name} must be timestamp after {#compare}";
        }
        $this->errorMsg($errorMsg);
        $this->compare = $compare;
    }

    protected function validate(Param $param, ServerRequestInterface $request): bool
    {
        $itemData = $param->parsedValue();
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