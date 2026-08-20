<?php

namespace EasySwoole\HttpAnnotation\Bean;

use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\ApiGroup;
use EasySwoole\HttpAnnotation\Attributes\ExtendParam;

class ClassAttribute
{

    public ApiGroup|null $apiGroup = null;

    public array $globalPreCall = [];

    public array $apis = [];

    public ClassOnRequest|null $onRequest;

    public array $methodPreCall = [];

    function __construct()
    {
        $this->onRequest = new ClassOnRequest();
    }

    function apiTag(string $method):Api|null
    {
        if(isset($this->apis[$method])){
            return $this->apis[$method];
        }
        return null;
    }

}