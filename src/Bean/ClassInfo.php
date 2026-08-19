<?php

namespace EasySwoole\HttpAnnotation\Bean;

use EasySwoole\HttpAnnotation\Attributes\ApiGroup;
use EasySwoole\HttpAnnotation\Attributes\ExtendParam;

class ClassInfo
{
    public ApiGroup|null $apiGroup = null;

    public array $globalParams = [];

    public ExtendParam|null $extendParam = null;

    public array $globalPreCall = [];

    public array $apis = [];

    public array $methods = [];
}