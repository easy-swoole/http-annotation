<?php

namespace EasySwoole\HttpAnnotation\Attributes;


use EasySwoole\HttpAnnotation\Exception\Annotation;

class Example implements \JsonSerializable
{
    const TYPE_PARAM_ARRAY = 1;
    const TYPE_DESCRIPTION = 2;
    private $exampleType = self::TYPE_PARAM_ARRAY;

    function __construct(
        public array|Description $example,
        public ?Description $description = null
    ){
        if(!is_array($this->example)){
            $this->exampleType = self::TYPE_DESCRIPTION;
        }
    }

    public function jsonSerialize(): mixed
    {
        $desc = $this->description;
        if(is_string($desc)){
            $desc = new Description($desc,Description::PLAIN_TEXT);
        }

        return [
            'example'=>$this->example,
            "description"=>$desc,
            'exampleType'=>$this->exampleType
        ];
    }
}