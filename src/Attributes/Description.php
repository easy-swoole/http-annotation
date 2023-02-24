<?php

namespace EasySwoole\HttpAnnotation\Attributes;

#[\Attribute]
class Description implements \JsonSerializable
{
    const PLAIN_TEXT = 1;
    const JSON = 2;
    const XML = 3;
    const MARKDOWN = 4;

    function __construct(public string $desc,public int $type = Description::PLAIN_TEXT)
    {
    }

    public function jsonSerialize(): mixed
    {
        return [
            "desc"=>$this->desc,
            "type"=>$this->type
        ];
    }
}