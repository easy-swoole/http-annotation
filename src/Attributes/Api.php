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
        public string        $apiName,
        public string        $allowMethod,
        public ?string       $requestPath = null,
        public bool          $registerRouter = false,
        public ?RequestParam $requestParam = null,
        public array         $requestExample = [],
        public array         $responseExample = [],
        public ?Description  $description = null,
        public bool          $deprecated = false,
    ){}
}