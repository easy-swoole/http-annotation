<?php

namespace EasySwoole\HttpAnnotation\Attributes\Validator;

use Psr\Http\Message\ServerRequestInterface;

#[\Attribute]
class Required extends AbstractValidator
{
    function __construct(?string $errorMsg = null)
    {
        $this->errorMsg = $errorMsg;
    }

    function validate($data, ServerRequestInterface $request): bool
    {
        return true;
    }

}