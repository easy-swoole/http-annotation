<?php

namespace EasySwoole\HttpAnnotation\Enum;

enum ParamFrom
{
    case GET;
    case POST;
    case XML;
    case JSON;
    case RAW_POST;
    case FILE;
    case DI;
    case CONTEXT;
    case COOKIE;
    case HEADER;
    case ROUTER_PARAMS;

    public function toString():string
    {
        return $this->name;
    }
}