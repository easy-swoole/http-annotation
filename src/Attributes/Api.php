<?php

namespace EasySwoole\HttpAnnotation\Attributes;

use EasySwoole\HttpAnnotation\Enum\HttpMethod;

#[\Attribute]
class Api implements \JsonSerializable
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

    public function jsonSerialize(): mixed
    {
        $allowMethods = $this->allowMethod;
        if(!is_array($allowMethods)){
            $allowMethods = [$allowMethods];
        }
        $temp = [];
        foreach ($allowMethods as $method){
            if($method instanceof HttpMethod){
                $temp[] = $method->name;
            }
        }

        $desc = $this->description;
        if(is_string($desc)){
            $desc = new Description($desc,Description::PLAIN_TEXT);
        }

        return [
            'apiName'=>$this->apiName,
            'allowMethod'=>$temp,
            'requestPath'=>$this->requestPath,
            'registerRouter'=>$this->registerRouter,
            'requestParam'=>$this->requestParam,
            'description'=>$desc
        ];
    }
}