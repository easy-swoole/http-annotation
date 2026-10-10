<?php

namespace EasySwoole\HttpAnnotation\Validator;

use BackedEnum;
use EasySwoole\HttpAnnotation\Exception\Annotation;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
use UnitEnum;

class Enum extends AbstractValidator
{
    protected string $enumClass;
    protected bool $strict;

    /** @param class-string<UnitEnum> $enumClass */
    public function __construct(string $enumClass, bool $strict = false, string|null $errorMsg = null)
    {
        if (!enum_exists($enumClass)) {
            throw new Annotation("Enum enumClass {$enumClass} must be a PHP enum");
        }
        $this->enumClass = $enumClass;
        $this->strict = $strict;
        if ($errorMsg !== null) {
            $this->errorMsgTpl($errorMsg);
        }
    }

    protected function validate(ValidateRequest $validateRequest): bool
    {
        $itemData = $validateRequest->validateParam->parsedValue();
        if ($itemData instanceof UnitEnum) {
            return $itemData instanceof $this->enumClass;
        }
        if (!is_int($itemData) && !is_string($itemData)) {
            return false;
        }

        foreach ($this->enumClass::cases() as $case) {
            if ($case instanceof BackedEnum) {
                // 有值枚举按 value 比较，默认兼容表单传入的数字字符串。
                if ($this->strict ? $itemData === $case->value : $itemData == $case->value) {
                    return true;
                }
            } elseif (is_string($itemData) && $itemData === $case->name) {
                // 无值枚举按名称精确匹配，区分大小写。
                return true;
            }
        }
        return false;
    }

    public static function ruleName(): string
    {
        return 'Enum';
    }
}
