<?php

namespace EasySwoole\HttpAnnotation;

use EasySwoole\Http\ReflectionCache;
use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\Utility\File;
use FastRoute\RouteCollector;

class Scanner
{
    static function mappingRouter(
        RouteCollector $routeCollector,
        string $controllerPath,
        string $controllerNameSpace = 'App\HttpController'
    ):void{
        $controllers = self::scanAllController($controllerPath,$controllerNameSpace);
        foreach ($controllers as $controller){
            $ref = ReflectionCache::getInstance()->getClassReflection($controller);
            $trimClass = ltrim(str_replace($controllerNameSpace,"",$controller),"\\");


            $methods = ReflectionCache::getInstance()->allowMethodReflections($ref);
            /** @var \ReflectionMethod $method */
            foreach ($methods as $method){
                if(!empty($method->getAttributes(Api::class))){

                }
            }
        }
    }

    private static function scanAllController(string $controllerPath,string $controllerNameSpace):array
    {
        $controllerNameSpace = trim($controllerNameSpace, '\\')."\\";
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
                ReflectionCache::getInstance()->addReflection($ref);
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