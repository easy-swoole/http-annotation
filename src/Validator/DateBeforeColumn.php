<?php

namespace EasySwoole\HttpAnnotation\Validator;

use EasySwoole\HttpAnnotation\Exception\Annotation;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;

class DateBeforeColumn extends AbstractValidator
{
    public $compare;

    public function __construct(string $compare, string|null $errorMsg = null)
    {
        $this->compare = $compare;
        if ($errorMsg !== null) {
            $this->errorMsgTpl($errorMsg);
        }
    }

    protected function validate(ValidateRequest $validateRequest): bool
    {
        $list = $validateRequest->allDefineParams;

        if (!isset($list[$this->compare])) {
            throw new Annotation("compare param: {$this->compare} require in DateBeforeColumn rule, but not define in any controller annotation");
        }

        $compareValue = $list[$this->compare]->parsedValue();

        // 定义存在但值无效属于请求校验失败，不作为配置异常抛出。
        if ((!is_string($compareValue) && !is_int($compareValue))
            || (is_string($compareValue) && trim($compareValue) === '')) {
            return false;
        }

        if (is_numeric($compareValue) && (strlen((string)$compareValue) == 10)) {
            $afterUnixTime = $compareValue;
        } else {
            $afterUnixTime = strtotime((string)$compareValue);
        }

        if (is_bool($afterUnixTime)) {
            return false;
        }

        $itemData = $validateRequest->validateParam->parsedValue();

        if (!is_string($itemData)) {
            return false;
        }

        $unixTime = strtotime($itemData);

        if (is_bool($unixTime)) {
            return false;
        }

        if ($unixTime < $afterUnixTime) {
            return true;
        }

        return false;
    }

    public static function ruleName(): string
    {
        return "DateBeforeColumn";
    }
}
