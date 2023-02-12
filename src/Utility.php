<?php

namespace EasySwoole\HttpAnnotation;

use EasySwoole\Http\ReflectionCache;
use EasySwoole\Http\UrlParser;
use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\ExtendParam;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Exception\Annotation;
use EasySwoole\Utility\File;
use FastRoute\RouteCollector;

class Utility
{
    public static function parseMethodParams(\ReflectionClass $reflectionClass,string $methodName):array
    {
        $temp = AttributeCache::getInstance()->getClassMethodParams($reflectionClass->name,$methodName);
        if($temp !== null){
            return $temp;
        }
        if(!$reflectionClass->hasMethod($methodName)){
            return [];
        }
        $actionMethodRef = $reflectionClass->getMethod($methodName);
        $actionApiTags = $actionMethodRef->getAttributes(Api::class);
        $finalParams = [];
        if(!empty($actionApiTags)){
            try{
                $apiTag = new Api(...$actionApiTags[0]->getArguments());
                /** @var Param $item */
                foreach ($apiTag->requestParam as $item){
                    $finalParams[$item->name] = $item;
                }
            }catch (\Throwable $exception){
                $msg = "{$exception->getMessage()} in controller: {$reflectionClass->name} method: {$methodName}";
                throw new Annotation($msg);
            }
        }else{
            $actionParamTags = $actionMethodRef->getAttributes(Param::class);
            foreach ($actionParamTags as $actionParamTag){
                try{
                    $param = new Param(...$actionParamTag->getArguments());
                    if(!isset($finalParams[$param->name])){
                        $finalParams[$param->name] = $param;
                    }else{
                        throw new Annotation("can not redefine param {$param->name}");
                    }
                }catch (\Throwable $exception){
                    $msg = "{$exception->getMessage()} in controller: {$reflectionClass->name} method: {$methodName}";
                    throw new Annotation($msg);
                }
            }
        }


        //检查是否继承父类
        $extendParent = $actionMethodRef->getAttributes(ExtendParam::class);
        if(!empty($extendParent)){
            $extendParent = new ExtendParam(...$extendParent[0]->getArguments());
            $parentClass = $reflectionClass->getParentClass();
            if($parentClass){
                $parentParams = self::parseMethodParams(new \ReflectionClass($parentClass->name),$methodName);
                if(!empty($extendParent->parentParams)){
                    $temp = [];
                    foreach ($extendParent->parentParams as $paramName){
                        if(isset($parentParams[$paramName])){
                            $temp[$paramName] = $parentParams[$paramName];
                        }else{
                            $msg = "param {$paramName} is not define in parent method {$methodName} of class {$reflectionClass->name}";
                            throw new Annotation($msg);
                        }
                    }
                    $parentParams = $temp;
                }
                foreach ($parentParams as $paramName => $param){
                    if(!isset($finalParams[$paramName])){
                        $finalParams[$paramName] = $param;
                    }
                }
            }
        }

        AttributeCache::getInstance()->setClassMethodParams($reflectionClass->name,$methodName,$finalParams);

        return $finalParams;
    }

    static function mappingRouter(
        RouteCollector $routeCollector,
        string $controllerPath,
        string $controllerNameSpace = 'App\HttpController'
    ):void{
        $controllers = self::scanAllController($controllerPath);
        foreach ($controllers as $controller){
            $ref = ReflectionCache::getInstance()->getClassReflection($controller);
            $trimClass = ltrim(str_replace($controllerNameSpace,"",$controller),"\\");
            $controllerRequestPrefix = str_replace("\\","/",$trimClass);
            //替换首字母为小写。
            $arr = explode("/",$controllerRequestPrefix);
            $controllerRequestPrefix = "";
            while ($a = array_shift($arr)){
                $controllerRequestPrefix .= lcfirst($a);
                if(!empty($arr)){
                    $controllerRequestPrefix .= "/";
                }
            }

            $methodRefs = ReflectionCache::getInstance()->allowMethodReflections($ref);
            /** @var \ReflectionMethod $methodRef */
            foreach ($methodRefs as $methodRef){
                if(!empty($methodRef->getAttributes(Api::class))){
                    $apiAttr = $methodRef->getAttributes(Api::class)[0];
                    try{
                        $apiAttr = new Api(...$apiAttr->getArguments());
                    }catch (\Throwable $exception){
                        $msg = "{$exception->getMessage()} in controller: {$controller} methodRef: {$methodRef->name}";
                        throw new Annotation($msg);
                    }
                    $realPath = "/{$controllerRequestPrefix}/{$methodRef->name}";
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

    static function scanAllController(string $controllerPath):array
    {
        $list = [];
        $files = [];
        if (is_file($controllerPath)) {
            $files[] = $controllerPath;
        } else {
            $temp = File::scanDirectory($controllerPath);
            if(isset($temp['files'])){
                $files = $temp['files'];
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
                $list[] = $class;
            }
        }

        return $list;
    }

    private static function getFileDeclaredClass(string $file): array
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