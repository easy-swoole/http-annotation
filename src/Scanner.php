<?php

namespace EasySwoole\HttpAnnotation;

use EasySwoole\Http\ReflectionCache;
use EasySwoole\Http\UrlParser;
use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\ApiGroup;
use EasySwoole\Utility\File;
use FastRoute\RouteCollector;

class Scanner
{
    static function mappingRouter(
        RouteCollector $routeCollector,
        string $controllerPath,
        string $controllerNameSpace = 'App\HttpController'
    ):void{
        $controllers = self::scanAllController($controllerPath);
        foreach ($controllers as $controller){
            $ref = ReflectionCache::getInstance()->getClassReflection($controller);
            $trimClass = ltrim(str_replace($controllerNameSpace,"",$controller),"\\");
            $controllerPrefix = str_replace("\\","/",$trimClass);

            $methods = ReflectionCache::getInstance()->allowMethodReflections($ref);
            /** @var \ReflectionMethod $method */
            foreach ($methods as $method){
                if(!empty($method->getAttributes(Api::class))){
                    $apiAttr = $method->getAttributes(Api::class)[0];
                    $apiAttr = new Api(...$apiAttr->getArguments());
                    $realPath = "/{$controllerPrefix}/{$method->name}";
                    if(!empty($apiAttr->requestPath) && $apiAttr->requestPath != $realPath){
                        if (!empty($apiAttr->allow)) {
                            $allow = $apiAttr->allow;
                        } else {
                            $allow = ['POST', 'GET', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'];
                        }
                        $routeCollector->addRoute($allow, UrlParser::pathInfo($apiAttr->requestPath), $realPath);
                    }

                }
            }
        }
    }

    static function scanToDoc(string $controllerPath):string{
        $finalDoc = "";
        $controllers = self::scanAllController($controllerPath);
        $groupInfos = [];

        foreach ($controllers as $controller){
            $reflection = ReflectionCache::getInstance()->getClassReflection($controller);
            $groupInfoRef = $reflection->getAttributes(ApiGroup::class);
            if(!empty($groupInfoRef)){
                /** @var \ReflectionAttribute $groupInfoRef */
                $groupInfoRef = $groupInfoRef[0];
                $name = $groupInfoRef->getArguments()['name'];
                var_dump($name);
            }
        }

        return $finalDoc;


    }

    private static function scanAllController(string $controllerPath,bool $save2Cache = true):array
    {
        $list = [];
        $files = [];
        if (is_file($controllerPath)) {
            $files[] = $controllerPath;
        } else {
            $files = File::scanDirectory($controllerPath);
            if(isset($files['files'])){
                $files = $files['files'];
            }else{
                return $list;
            }
        }

        foreach ($files as $file) {
            $fileExtension = pathinfo($file)['extension'] ?? '';
            if ($fileExtension != 'php') {
                continue;
            }
            $class = static::getFileDeclaredClass($file);
            if (empty($class)) {
                continue;
            }else{
                $class = $class[0];
            }
            $ref = new \ReflectionClass($class);
            if($ref->isSubclassOf(AnnotationController::class)){
                if($save2Cache){
                    ReflectionCache::getInstance()->addReflection($ref);
                }
                $list[] = $class;
            }
        }

        return $list;
    }

    static function getFileDeclaredClass(string $file): array
    {

        $namespace = null;
        $matchNamespace = false;
        $matchClass = false;
        $classes = [];
        foreach (token_get_all(file_get_contents($file)) as $line => $info){
            if(($info[0] == T_NAMESPACE) && $namespace === null){
                $matchNamespace = true;
                continue;
            }
            if(($info[0] == T_NAME_QUALIFIED) && $matchNamespace){
                $namespace = $info[1];
                $matchNamespace = false;
                continue;
            }
            if($info[0] == T_CLASS){
                $matchClass = true;
                continue;
            }
            if($matchClass && $info[0] == T_STRING){
                $classes[] = $info[1];
                $matchClass = false;
            }
        }



        $ret = [];

        foreach ($classes as $class){
            $class = ltrim($class,"\\");
            if($namespace !== null){
                $ret[] = $namespace."\\".$class;
            }else{
                $ret[] = $class;
            }
        }

        return $ret;

    }
}