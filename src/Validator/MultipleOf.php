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

        $quotient = (float)$value / $this->multiple;
        if (!is_finite($quotient)) {
            return false;
        }
        // Allow binary floating-point rounding, but keep tolerance below 1e-9 multiples.
        $tolerance = min(1e-9, 8 * PHP_FLOAT_EPSILON * max(1.0, abs($quotient)));
        return abs($quotient - round($quotient)) <= $tolerance;
    }

    public static function ruleName(): string
    {
        return 'MultipleOf';
    }
}
