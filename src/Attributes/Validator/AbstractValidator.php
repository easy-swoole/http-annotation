<?php

namespace EasySwoole\HttpAnnotation\Attributes\Validator;

use Psr\Http\Message\ServerRequestInterface;

abstract class AbstractValidator
{
    /**
     * @var string|null
     */
    protected $errorMsg;

    abstract function validate($data,ServerRequestInterface $request):bool;

    function getErrorMsg():?string
    {
        return $this->errorMsg;
    }
}