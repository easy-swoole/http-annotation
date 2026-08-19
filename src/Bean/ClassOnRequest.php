<?php

namespace EasySwoole\HttpAnnotation\Bean;

use EasySwoole\HttpAnnotation\Attributes\ExtendParam;

class ClassOnRequest
{
    public ExtendParam|null $extendParam = null;

    public array $onRequestParams = [];
}