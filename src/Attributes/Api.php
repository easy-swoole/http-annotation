<?php

namespace EasySwoole\HttpAnnotation\Attributes;

use EasySwoole\HttpAnnotation\Enum\HttpMethod;

#[\Attribute]
class Api
{
    function __construct(
        public string        $apiName,
        public HttpMethod|array        $allowMethod = [HttpMethod::GET,HttpMethod::POST],
        public ?string       $requestPath = null,
        public bool          $registerRouter = false,
        public array         $requestParam = [],
        public array         $responseParam = [],
        public array         $requestExample = [],
        public array         $responseExample = [],
        public Description|string|null $description = null,
        public bool          $deprecated = false,
    ){}
}