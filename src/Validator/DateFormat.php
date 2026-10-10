<?php

namespace EasySwoole\HttpAnnotation\Validator;

use DateTime;
use ValueError;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
use Psr\Http\Message\ServerRequestInterface;

class DateFormat extends AbstractValidator
{
    protected string $format;
    function __construct(string $dateFormat,string|null $errorMsg = null)
    {
        $this->format = $dateFormat;
        if ($errorMsg !== null) {
            $this->errorMsgTpl($errorMsg);
        }
    }

    protected function validate(ValidateRequest $validateRequest): bool
    {
        $itemData = $validateRequest->validateParam->parsedValue();
        if(empty($itemData)){
            return false;
        }

        if (!is_string($itemData)) {
            return false;
        }
        try {
            $test = DateTime::createFromFormat($this->format, $itemData);
        } catch (ValueError) {
            return false;
        }
        $errors = DateTime::getLastErrors();
        return $test !== false
            && ($errors === false || ($errors['warning_count'] === 0 && $errors['error_count'] === 0));

    }

    public static function ruleName(): string
    {
        return "DateFormat";
    }
}