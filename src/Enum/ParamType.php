<?php

namespace EasySwoole\HttpAnnotation\Enum;

enum ParamType
{
    case STRING;
    case INT;
    case DOUBLE;
    case REAL;
    case FLOAT;
    case BOOLEAN;
    case LIST;
    case OBJECT;
    case FILE;
}