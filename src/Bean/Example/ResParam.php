<?php

namespace EasySwoole\HttpAnnotation\Bean\Example;

use EasySwoole\HttpAnnotation\Bean\Description\Text;

class ResParam
{
    public function __construct(
        public string $name,
        public mixed $exampleValue,
        public ResParamType $paramType = ResParamType::string,
        public string|null|Text        $description = null
    )
    {

    }
}