<?php

namespace EasySwoole\HttpAnnotation\Attributes;

#[\Attribute(\Attribute::TARGET_CLASS)]
class ApiGroup implements \JsonSerializable
{
    function __construct(public string $groupName, public Description|string|null $description = null){}

    public function jsonSerialize(): mixed
    {
        $desc = $this->description;
        if(is_string($desc)){
            $desc = new Description($desc,Description::PLAIN_TEXT);
        }
        return [
            'groupName'=>$this->groupName,
            'description'=>$desc
        ];
    }
}