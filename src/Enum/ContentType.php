<?php

namespace EasySwoole\HttpAnnotation\Enum;

enum ContentType
{
    case FORM_DATA;

    case FORM_URLENCODED;

    case JSON;

    case XML;

    case RAW;
}