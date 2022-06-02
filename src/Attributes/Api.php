<?php

namespace EasySwoole\HttpAnnotation\Attributes;

#[\Attribute]
class Api
{
    const GET = "GET";
    const POST = "POST";

    function __construct(
        public string $requestPath,
        public string $apiName,
        public string $apiGroup,
        public bool $registerRouter = false,
        public array $allow = [Api::GET,Api::POST],
        public array $params = [],
        public array $exampleParams = [],
        public array $exampleSuccess = [],
        public array $exampleFail = [],
        public ?Description $description = null
    ){}
}