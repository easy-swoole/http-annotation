<?php

namespace EasySwoole\HttpAnnotation\Attributes;

#[\Attribute]
class Api
{
    const GET = "GET";
    const POST = "POST";
    const PUT = 'PUT';
    const PATCH = "PATCH";
    const DELETE = "DELETE";
    const HEAD = "HEAD";
    const OPTIONS = "OPTIONS";

    function __construct(
        public string $apiName,
        public ?string $requestPath = null,
        public ?string $apiGroup = null,
        public bool $registerRouter = false,
        public array $allowMethod = [Api::GET,Api::POST],
        public array $params = [],
        public array $exampleParams = [],
        public array $exampleSuccess = [],
        public array $exampleFail = [],
        public ?Description $description = null
    ){}
}