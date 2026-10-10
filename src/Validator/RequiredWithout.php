<?php

namespace EasySwoole\HttpAnnotation\Validator;

use EasySwoole\HttpAnnotation\Exception\Annotation;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;

class RequiredWithout extends AbstractValidator
{
    protected array $paramNames;

    public function __construct(array $paramNames, string|null $errorMsg = null)
    {
        if ($paramNames === []) {
            throw new Annotation('RequiredWithout paramNames must not be empty');
        }
        foreach ($paramNames as $paramName) {
            if (!is_string($paramName) || trim($paramName) === '') {
                throw new Annotation('RequiredWithout parameter names must be non-empty strings');
            }
        }
        $this->paramNames = $paramNames;
        if ($errorMsg !== null) {
            $this->errorMsgTpl($errorMsg);
        }
    }

    protected function validate(ValidateRequest $validateRequest): bool
    {
        $list = $validateRequest->allDefineParams;
        // Check all definitions before evaluating the condition.
        foreach ($this->paramNames as $paramName) {
            if (!isset($list[$paramName])) {
                throw new Annotation("RequiredWithout on parameter {$validateRequest->validateParam->name} references undefined parameter {$paramName} in {$validateRequest->callClass}::{$validateRequest->callMethod}");
            }
        }
        $required = false;
        foreach ($this->paramNames as $paramName) {
            $param = $list[$paramName];
            $compare = $param->parsedValue();
            if (!$param->hasSet() || $compare === null || $compare === '' || $compare === []) {
                $required = true;
                break;
            }
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
        return 'RequiredWithout';
    }
}
