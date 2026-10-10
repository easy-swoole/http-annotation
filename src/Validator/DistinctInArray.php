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

        $seen = [];
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
