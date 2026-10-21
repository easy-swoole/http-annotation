<?php

namespace EasySwoole\HttpAnnotation\Bean\Example;

enum ResParamType: string
{
    case object = 'object';

    case array = 'array';

    case string = 'string';

    case int = 'int';

    case bool = 'bool';

    case float = 'float';
}
