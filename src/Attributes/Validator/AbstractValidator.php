<?php

namespace EasySwoole\HttpAnnotation\Attributes\Validator;

use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\Param;
use Psr\Http\Message\ServerRequestInterface;

abstract class AbstractValidator
{
    /**
     * @var string|null
     */
    protected $errorMsg;

    protected Param $param;

    protected Api $api;

    abstract function validate(ServerRequestInterface $request):bool;

    function setParamAttr(Param $param):AbstractValidator
    {
        $this->param = $param;
        return $this;
    }

    function setApiAttr(Api $api):AbstractValidator
    {
        $this->api = $api;
        return $this;
    }

    function getErrorMsg():?string
    {
        return $this->errorMsg;
    }

    protected function isIgnoreCheck():bool
    {
        //当配置了option选项，且传参不是null,也就是没传的时候，允许忽略检查
        if($this->param->isOptional() && (!$this->param->isNullData()) && ($this->param->parsedValue() === null)){
            return true;
        }
        return false;
    }
}