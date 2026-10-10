<?php

namespace EasySwoole\HttpAnnotation\Validator;

use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;

class DistinctInArray extends AbstractValidator
{
    public function __construct(protected bool $strict = false, ?string $errorMsg = null)
    {
        if ($errorMsg !== null) {
            $this->errorMsgTpl($errorMsg);
        }
    }

    protected function validate(ValidateRequest $validateRequest): bool
    {
        $values = $validateRequest->validateParam->parsedValue();
        if (!is_array($values)) {
            return false;
        }

        // 先检查元素类型，特殊数值字符串在宽松模式下保留 PHP 原有比较行为。
        $useHash = true;
        foreach ($values as $value) {
            if (!is_int($value) && !is_string($value)) {
                return false;
            }
            if (!$this->strict && is_string($value) && is_numeric($value)
                && filter_var($value, FILTER_VALIDATE_INT) === false) {
                $useHash = false;
            }
        }

        $seen = [];
        if ($useHash) {
            foreach ($values as $value) {
                if (is_int($value)) {
                    $key = 'int:' . $value;
                } elseif (!$this->strict && is_numeric($value)) {
                    // 宽松模式下将整数形式字符串归一化，令 1 与 '1' 命中同一个键。
                    $key = 'int:' . filter_var($value, FILTER_VALIDATE_INT);
                } else {
                    // 前缀避免 PHP 数组自动转换字符串键，同时保留严格模式的类型区别。
                    $key = 'string:' . $value;
                }
                if (isset($seen[$key])) {
                    return false;
                }
                $seen[$key] = true;
            }
            return true;
        }

        // 小数、科学计数法、超大整数等字符串的宽松比较不能直接用字符串键替代。
        foreach ($values as $value) {
            if (in_array($value, $seen, $this->strict)) {
                return false;
            }
            $seen[] = $value;
        }
        return true;
    }

    public static function ruleName(): string
    {
        return 'DistinctInArray';
    }
}
