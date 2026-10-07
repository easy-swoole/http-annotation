<?php

namespace EasySwoole\HttpAnnotation\Attributes;

use EasySwoole\HttpAnnotation\Bean\Description\Markdown;
use EasySwoole\HttpAnnotation\Bean\Description\Text;

#[\Attribute(\Attribute::TARGET_CLASS)]
class ApiGroup
{
    public string $relateClass;

    function __construct(
        public string $groupName,
        public string|null|Markdown|Text $description = null,
    ){
        if(is_string($this->description)){
            $this->description = new Text($this->description);
        }
    }
}