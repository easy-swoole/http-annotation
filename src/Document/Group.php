<?php

namespace EasySwoole\HttpAnnotation\Document;

use EasySwoole\HttpAnnotation\Attributes\Api;

class Group implements \JsonSerializable
{

    private array $apis = [];

    function __construct(
        private string $name,
        private string|null $description = null
    ){}

    function getName():string
    {
        return $this->name;
    }

    function getDescription(): string|null
    {
        return $this->description;
    }

    function setDescription(string|null $description)
    {
        $this->description = $description;
    }

    function getApis():array
    {
        return $this->apis;
    }

    function addApi(Api $api):bool
    {
        if(isset($this->apis[$api->apiName])){
            return false;
        }
        $this->apis[$api->apiName] = $api;
        return true;
    }

    public function jsonSerialize(): mixed
    {
        return [
            "groupName"=>$this->name,
            'description'=>$this->description,
            "apiList"=>$this->apis
        ];
    }
}