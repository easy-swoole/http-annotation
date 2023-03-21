<?php

namespace EasySwoole\HttpAnnotation\Attributes;


use EasySwoole\HttpAnnotation\Exception\Annotation;

class Example implements \JsonSerializable
{
    const TYPE_PARAM_ARRAY = 'PARAM_ARRAY';
    const TYPE_DESCRIPTION = 'DESCRIPTION';
    private string $exampleType = self::TYPE_PARAM_ARRAY;

    function __construct(
        public array|Description|string $example
    ){

        if(!is_array($this->example)){
            if(is_string($this->example)){
                $this->example = new Description($this->example);
            }
            $this->exampleType = self::TYPE_DESCRIPTION;
        }
    }

    public function jsonSerialize(): mixed
    {
        return [
            'example'=>$this->example,
            'exampleType'=>$this->exampleType
        ];
    }
}