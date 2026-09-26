<?php

namespace EasySwoole\HttpAnnotation\Tests\ControllerExample;

use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use EasySwoole\HttpAnnotation\Exception\Annotation;

class Utility
{
    public static function preCall(Request $request,Response $response): bool
    {
        return true;
    }

    public static function preCallGlobal(): bool
    {
        return true;
    }
}