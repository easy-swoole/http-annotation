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
    case FILE;
    case NULL_WHILE_EMPTY;
}