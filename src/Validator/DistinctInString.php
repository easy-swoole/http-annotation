<?php

namespace EasySwoole\HttpAnnotation\Validator;

use EasySwoole\HttpAnnotation\Exception\Annotation;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;

class DistinctInString extends AbstractValidator
{
    public function __construct(protected string $separator = ',', ?string $errorMsg = null)
    {
        if ($separator === '') {
            throw new Annotation('DistinctInString separator must not be empty');
        }
        if ($errorMsg !== null) {
            $this->errorMsgTpl($errorMsg);
        }
    }

    protected function validate(ValidateRequest $validateRequest): bool
    {
        $value = $validateRequest->validateParam->parsedValue();
        if (!is_string($value)) {
            return false;
        }

        $items = explode($this->separator, $value);
        return count($items) === count(array_unique($items, SORT_STRING));
    }

    public static function ruleName(): string
    {
        return 'DistinctInString';
    }
}
