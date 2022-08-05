<?php

namespace EasySwoole\HttpAnnotation\Attributes;

#[\Attribute]
class Description
{
    const PLAIN_TEXT = 1;
    const JSON = 2;
    const XML = 3;
    const MARKDOWN = 4;

    function __construct(public string $desc,public int $type = Description::PLAIN_TEXT)
    {
    }
}