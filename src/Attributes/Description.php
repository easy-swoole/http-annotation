<?php

namespace EasySwoole\HttpAnnotation\Attributes;

#[\Attribute]
class Description implements \JsonSerializable
{
    const PLAIN_TEXT = 1;
    const JSON = 20;
    const JSON_FILE = 21;
    const XML = 30;

    const XML_FILE = 31;

    function __construct(public string $desc,public int $type = Description::PLAIN_TEXT)
    {

    }

    public function jsonSerialize(): mixed
    {
        if($this->type == self::JSON){
            $des = json_decode($this->desc,true);
            $des = json_encode($des,JSON_PRETTY_PRINT);
        }else{
            $des = $this->desc;
        }
        return [
            "desc"=>$des,
            "type"=>$this->type
        ];
    }
}