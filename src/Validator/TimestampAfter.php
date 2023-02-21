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

        if (is_numeric($itemData) && is_numeric($this->compare)) {
            return intval($itemData) > intval($this->compare);
        }else{
            $itemData = $param->parsedValue();
            $unixTime = strtotime($itemData);
            if(is_bool($unixTime)){
                return false;
            }
            $compare = strtotime($this->compare);

            if (is_bool($compare)) {
                throw new Annotation("error arg:compare for TimestampAfter validate rule");
            }

            if ($unixTime > $unixTime) {
                return true;
            }
        }

        return false;
    }

    function ruleName(): string
    {
        return "TimestampAfter";
    }
}