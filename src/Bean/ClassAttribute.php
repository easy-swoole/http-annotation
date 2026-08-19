<?php

namespace EasySwoole\HttpAnnotation\Bean;

use EasySwoole\HttpAnnotation\Attributes\ApiGroup;
use EasySwoole\HttpAnnotation\Attributes\ExtendParam;

class ClassAttribute
{

    public ApiGroup|null $apiGroup = null;

    public array $globalPreCall = [];

    public array $apis = [];

    public ClassOnRequest|null $onRequest;

    function __construct()
    {
        $this->onRequest = new ClassOnRequest();
    }

}