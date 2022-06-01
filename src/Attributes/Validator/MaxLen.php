<?php

namespace EasySwoole\HttpAnnotation\Attributes\Validator;

use Psr\Http\Message\ServerRequestInterface;

#[\Attribute]
class MaxLen extends AbstractValidator
{
    protected $maxLen;

    function __construct(int $maxLen,?string $errorMsg = null)
    {
        $this->maxLen = $maxLen;
        $this->errorMsg = $errorMsg;
    }

    function validate($data, ServerRequestInterface $request): bool
    {
        return  true;
    }
}