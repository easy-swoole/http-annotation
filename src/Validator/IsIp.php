<?php

namespace EasySwoole\HttpAnnotation\Validator;

use EasySwoole\HttpAnnotation\Exception\Annotation;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;

class IsIp extends AbstractValidator
{
    protected string $mode;

    public function __construct(string $mode = 'ANY', ?string $errorMsg = null)
    {
        $this->mode = strtoupper($mode);
        if (!in_array($this->mode, ['ANY', 'IPV4', 'IPV6'], true)) {
            throw new Annotation('IsIp mode must be ANY, IPV4 or IPV6');
        }
        if ($errorMsg !== null) {
            $this->errorMsgTpl($errorMsg);
        }
    }

    protected function validate(ValidateRequest $validateRequest): bool
    {
        $flags = match ($this->mode) {
            'IPV4' => FILTER_FLAG_IPV4,
            'IPV6' => FILTER_FLAG_IPV6,
            default => 0,
        };
        return filter_var($validateRequest->validateParam->parsedValue(), FILTER_VALIDATE_IP, $flags) !== false;
    }

    public static function ruleName(): string
    {
        return "IsIp";
    }
}
