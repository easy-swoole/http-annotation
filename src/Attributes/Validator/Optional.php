<?php

namespace EasySwoole\HttpAnnotation\Attributes\Validator;

use Psr\Http\Message\ServerRequestInterface;

#[\Attribute]
class Optional extends AbstractValidator
{
    function validate(ServerRequestInterface $request): bool
    {
        return true;
    }
}