<?php

namespace EasySwoole\HttpAnnotation;

use EasySwoole\Component\Singleton;
use EasySwoole\HttpAnnotation\Attributes\ApiGroup;

class AttributeCache
{
    use Singleton;

    protected array $apiGroup = [];

    function addApiGroup(ApiGroup $apiGroup):bool
    {
        if(isset($this->apiGroup[$apiGroup->name])){
            return false;
        }else{
            $this->apiGroup[$apiGroup->name] = $apiGroup;
            return true;
        }
    }

    function apiGroups():array
    {
        return $this->apiGroup;
    }
}