<?php

namespace EasySwoole\HttpAnnotation\Validator;

use EasySwoole\HttpAnnotation\Exception\Annotation;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;

class RequiredIf extends AbstractValidator
{
    protected string $paramName;
    protected mixed $value;
    protected bool $strict;

    public function __construct(string $paramName, mixed $value, bool $strict = false, string|null $errorMsg = null)
    {
        if (trim($paramName) === '') {
            throw new Annotation('RequiredIf parameter name must be a non-empty string');
        }
        $this->paramName = $paramName;
        $this->value = $value;
        $this->strict = $strict;
        if ($errorMsg !== null) {
            $this->errorMsgTpl($errorMsg);
        }
    }

    protected function validate(ValidateRequest $validateRequest): bool
    {
        $list = $validateRequest->allDefineParams;
        if (!isset($list[$this->paramName])) {
            throw new Annotation("RequiredIf on parameter {$validateRequest->validateParam->name} references undefined parameter {$this->paramName} in {$validateRequest->callClass}::{$validateRequest->callMethod}");
        }
        $param = $list[$this->paramName];
        if (!$param->hasSet()) {
            return true;
        }
        $compare = $param->parsedValue();
        if ($this->strict) {
            $required = $compare === $this->value;
        } else {
            $required = $compare == $this->value;
        }
        if (!$required) {
            return true;
        }

        $param = $validateRequest->validateParam;
        $itemData = $param->parsedValue();
        return $param->hasSet() && $itemData !== null && $itemData !== '' && $itemData !== [];
    }

    public static function ruleName(): string
    {
        return 'RequiredIf';
    }
}
