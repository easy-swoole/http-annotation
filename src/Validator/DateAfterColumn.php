<?php

namespace EasySwoole\HttpAnnotation\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Exception\Annotation;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
use Psr\Http\Message\ServerRequestInterface;

class DateAfterColumn extends AbstractValidator
{
    public $compare;

    public function __construct(string $compare, string|null $errorMsg = null)
    {
        if (empty($errorMsg)) {
            $errorMsg = "{#name} must be date after {#compare} column";
        }
        $this->compare = $compare;
        $this->errorMsgTpl($errorMsg);
    }

    protected function validate(ValidateRequest $validateRequest): bool
    {
        $list = $validateRequest->allDefineParams;

        if (!isset($list[$this->compare])) {
            throw new Annotation("compare param: {$this->compare} require in DateAfterColumn rule, but not define in any controller annotation");
        }

        $compareValue = $list[$this->compare]->parsedValue();

        if (is_numeric($compareValue) && (strlen($compareValue) == 10)) {
            $afterUnixTime = $compareValue;
        } else {
            $afterUnixTime = strtotime($compareValue);
        }

        if (is_bool($afterUnixTime)) {
            throw new Annotation("error arg: error compare param: {$this->compare} for DateAfterColumn validate rule");
        }

        $itemData = $validateRequest->validateParam->parsedValue();

        if (!is_string($itemData)) {
            return false;
        }

        $unixTime = strtotime($itemData);

        if (is_bool($unixTime)) {
            return false;
        }

        if ($unixTime > $afterUnixTime) {
            return true;
        }

        return false;
    }

    public function ruleName(): string
    {
        return "DateAfterColumn";
    }
}
