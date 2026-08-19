<?php

namespace EasySwoole\HttpAnnotation\Attributes;

#[\Attribute(\Attribute::TARGET_CLASS||\Attribute::TARGET_METHOD)]
class ExtendParam
{
    public function __construct(
        public array|null $parentParamsName = []
    )
    {

    }
}