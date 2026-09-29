<?php

namespace EasySwoole\HttpAnnotation\Attributes;

#[\Attribute(\Attribute::TARGET_CLASS)]
class ApiGroup implements \JsonSerializable
{
    function __construct(public string $groupName, public string|null $description = null){}

    public function jsonSerialize(): mixed
    {
        return [
            'groupName'=>$this->groupName,
            'description'=>$this->description,
        ];
    }
}