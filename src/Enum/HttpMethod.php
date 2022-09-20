<?php

namespace EasySwoole\HttpAnnotation\Enum;

enum HttpMethod
{
    case GET;
    case POST;
    case PUT;
    case DELETE;
    case HEAD;
    case PATCH;
    case OPTIONS;

    function toString():string
    {
        return $this->name;
    }

}