<?php

namespace EasySwoole\HttpAnnotation\Attributes;

use EasySwoole\HttpAnnotation\Enum\HttpMethod;

#[\Attribute]
class Api
{
    public string $relateClass;
    public string $relateMethod;

    function __construct(
        public string        $apiName,
        public HttpMethod|array        $allowMethod = [HttpMethod::GET,HttpMethod::POST],
        public string|null       $requestPath = null,
        public bool          $registerRouter = false,
        public array         $requestParam = [],
        public array         $responseParam = [],
        public array         $requestExamples = [],
        public array         $responseExamples = [],
        public string|null   $description = null,
        public bool          $deprecated = false,
    ){
        $temp = [];
        /** @var Param $item */
        foreach ($this->requestParam as $item){
            $temp[$item->name] = $item;
        }
        $this->requestParam = $temp;
    }
}