<?php

namespace EasySwoole\HttpAnnotation\Attributes;

#[\Attribute(\Attribute::TARGET_METHOD)]
class ExtendParentParams
{
    public function __construct(
        public ?array $params = []
    )
    {

    }
}