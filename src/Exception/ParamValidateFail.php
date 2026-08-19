<?php

namespace EasySwoole\HttpAnnotation\Exception;

use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;

class ParamValidateFail extends Annotation
{
    private AbstractValidator $failRule;

    private string $paramName;

    public function getFailRule(): AbstractValidator
    {
        return $this->failRule;
    }

    public function setFailRule(AbstractValidator $failRule): void
    {
        $this->failRule = $failRule;
    }

    public function getParamName(): string
    {
        return $this->paramName;
    }

    public function setParamName(string $paramName): void
    {
        $this->paramName = $paramName;
    }

}