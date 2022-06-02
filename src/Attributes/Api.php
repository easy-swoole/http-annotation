<?php

namespace EasySwoole\HttpAnnotation\Attributes;

#[\Attribute]
class Api
{
    const GET = "GET";
    const POST = "POST";

    function __construct(
        string $requestPath,
        string $apiName,
        string $apiGroup,
        bool $registerRouter = false,
        array $allow = [Api::GET,Api::POST],
        array $params = [],
        array $exampleParams = [],
        array $exampleSuccess = [],
        array $exampleFail = [],
        Description $description = null
    ){}
}