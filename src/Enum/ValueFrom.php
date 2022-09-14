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
}