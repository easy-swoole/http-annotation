<?php

namespace EasySwoole\HttpAnnotation\Validator;

use EasySwoole\HttpAnnotation\Exception\Annotation;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;

class MultipleOf extends AbstractValidator
{
    public function __construct(protected int|float $multiple, ?string $errorMsg = null)
    {
        if (!is_finite((float)$multiple) || $multiple == 0) {
            throw new Annotation('MultipleOf multiple must be finite and non-zero');
        }
        if ($errorMsg !== null) {
            $this->errorMsgTpl($errorMsg);
        }
    }

    protected function validate(ValidateRequest $validateRequest): bool
    {
        $value = $validateRequest->validateParam->parsedValue();
        if (!is_numeric($value) || !is_finite((float)$value)) {
            return false;
        }

        // Preserve exact integer arithmetic, including integers beyond float precision.
        $integer = is_int($value) ? $value : (is_string($value)
            ? filter_var($value, FILTER_VALIDATE_INT) : false);
        if ($integer !== false && is_int($this->multiple)) {
            return $integer % $this->multiple === 0;
        }

        $numericValue = (float)$value;
        if ($numericValue == 0.0) {
            if (is_string($value)) {
                // 只检查尾数，区分真实零与转换时下溢的非零字符串（例如 1e-999）。
                $mantissa = preg_split('/e/i', trim($value), 2)[0];
                return preg_match('/[1-9]/', $mantissa) === 0;
            }
            return true;
        }

        $quotient = $numericValue / $this->multiple;
        if (!is_finite($quotient)) {
            return false;
        }
        $nearestInteger = round($quotient);
        // 非零输入不能靠容差匹配零，也不能在除法下溢后被视为零的倍数。
        if ($nearestInteger == 0.0) {
            return false;
        }
        // Allow binary floating-point rounding, but keep tolerance below 1e-9 multiples.
        $tolerance = min(1e-9, 8 * PHP_FLOAT_EPSILON * max(1.0, abs($quotient)));
        return abs($quotient - $nearestInteger) <= $tolerance;
    }

    public static function ruleName(): string
    {
        return 'MultipleOf';
    }
}
