<?php

namespace EasySwoole\HttpAnnotation;

use FastRoute\RouteCollector;

class Utility
{
    public static function parseMethodParams(\ReflectionClass $reflectionClass,string $methodName):array
    {
        return [];
    }

    static function mappingRouter(
        RouteCollector $routeCollector,
        string $controllerPath,
        string $controllerNameSpace = 'App\HttpController'
    ):void{

    }
}