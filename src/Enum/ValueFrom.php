<?php

namespace EasySwoole\HttpAnnotation\Enum;

enum ValueFrom
{
    case GET;
    case FORM_POST;
    case XML;
    case JSON;
    case RAW_POST;
    case FILE;
    case DI;
    case CONTEXT;
    case COOKIE;
    case HEADER;
    case ROUTER_PARAMS;
}