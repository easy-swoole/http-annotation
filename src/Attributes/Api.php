<?php

namespace EasySwoole\HttpAnnotation\Attributes;

#[\Attribute]
class Api
{
    const GET = "GET";
    const POST = "POST";

    function __construct(
        string $path,
        bool $registerRouter = false,
        array $allow = [Api::GET,Api::POST],
        array $params = [],
        array $success = [],
        array $fail = [],
        array $requestExample = []
    ){}
}