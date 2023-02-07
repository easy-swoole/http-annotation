<?php

namespace EasySwoole\HttpAnnotation\Attributes;

#[\Attribute(\Attribute::TARGET_METHOD)]
class ExtendParam
{
    public function __construct(
        public ?array $parentParams = []
    )
    {

    }
}