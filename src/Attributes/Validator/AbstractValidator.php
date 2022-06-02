<?php

namespace EasySwoole\HttpAnnotation\Attributes\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use Psr\Http\Message\ServerRequestInterface;

abstract class AbstractValidator
{
    /**
     * @var string|null
     */
    protected $errorMsg;

    protected Param $param;

    abstract function validate($column,ServerRequestInterface $request):bool;

    function setParam(Param $param):AbstractValidator
    {
        $this->param = $param;
        return $this;
    }

    function getErrorMsg():?string
    {
        return $this->errorMsg;
    }
}