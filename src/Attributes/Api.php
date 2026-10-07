<?php

namespace EasySwoole\HttpAnnotation\Attributes;

use EasySwoole\HttpAnnotation\Bean\Description\Markdown;
use EasySwoole\HttpAnnotation\Bean\Description\Text;
use EasySwoole\HttpAnnotation\Bean\Example\ExampleInterface;
use EasySwoole\HttpAnnotation\Enum\HttpMethod;
use EasySwoole\HttpAnnotation\Exception\Annotation;

#[\Attribute]
class Api
{
    public string $relateClass;
    public string $relateMethod;
    public string $apiName;
    public string|null       $requestPath = null;

    function __construct(
        public HttpMethod|array        $allowMethod = [HttpMethod::GET,HttpMethod::POST],
        public bool          $registerRouter = false,
        public array         $requestParam = [],
        public array         $responseParam = [],
        /**
         * @var array<ExampleInterface>
         */
        public array         $requestExamples = [],
        /**
         * @var array<ExampleInterface>
         */
        public array         $responseExamples = [],
        public string|null|Markdown|Text   $description = null,
        public bool          $deprecated = false,
    ){
        $temp = [];
        /** @var Param $item */
        foreach ($this->requestParam as $item){
            if(isset($temp[$item->name])){
                throw new Annotation("param {$item->name} define duplicate");
            }
            $temp[$item->name] = $item;
        }
        $this->requestParam = $temp;
        if(is_string($this->description)){
            $this->description = new Text($this->description);
        }
    }
}