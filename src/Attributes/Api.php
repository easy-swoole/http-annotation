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
        public bool $registerRouter = false,
        public array $allowMethod = [Api::GET,Api::POST],
        public array $params = [],
        public array $requestExample = [],
        public array $successExample = [],
        public array $failExample = [],
        public ?Description $description = null
    ){}
}