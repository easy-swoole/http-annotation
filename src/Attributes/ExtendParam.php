<?php

namespace EasySwoole\HttpAnnotation\Attributes;


/** 仅对onRequest方法有效 */
#[\Attribute(\Attribute::TARGET_METHOD)]
class ExtendParam
{
    public function __construct(
        public array|null $parentParamsName = []
    )
    {}
}