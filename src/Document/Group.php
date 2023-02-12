<?php

namespace EasySwoole\HttpAnnotation\Document;

use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\Description;

class Group
{

    private array $apis = [];

    function __construct(
        private string $name,
        private Description|string|null $description = null
    ){}

    function getName():string
    {
        return $this->name;
    }

    function getDescription(): string|Description|null
    {
        return $this->description;
    }

    function setDescription(string|Description|null $description)
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
}