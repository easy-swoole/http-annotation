<?php

namespace EasySwoole\HttpAnnotation\Exception;

use EasySwoole\Http\Exception\Exception;
use EasySwoole\HttpAnnotation\Attributes\Validator\AbstractValidator;

class ValidateFail extends Exception
{
    private ?AbstractValidator $failRule;

    /**
     * @return AbstractValidator|null
     */
    public function getFailRule(): ?AbstractValidator
    {
        return $this->failRule;
    }

    /**
     * @param AbstractValidator|null $failRule
     */
    public function setFailRule(?AbstractValidator $failRule): void
    {
        $this->failRule = $failRule;
    }

}