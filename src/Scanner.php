<?php

namespace EasySwoole\HttpAnnotation;

use EasySwoole\Http\ReflectionCache;
use EasySwoole\Http\UrlParser;
use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\ApiGroup;
use EasySwoole\HttpAnnotation\Attributes\Description;
use EasySwoole\HttpAnnotation\Exception\Annotation;
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
        $groupDetail = [];
        $groupApiMethods = [];

        foreach ($controllers as $controller){
            $reflection = ReflectionCache::getInstance()->getClassReflection($controller);
            $groupInfoRef = $reflection->getAttributes(ApiGroup::class);
            $description = null;
            if(!empty($groupInfoRef)){
                /** @var \ReflectionAttribute $groupInfoRef */
                $groupInfoRef = $groupInfoRef[0];
                $arg = $groupInfoRef->getArguments();
                $groupName = $arg['groupName'];
                if(isset($arg["description"])){
                    $description = $arg['description'];
                }
            }else{
                $groupName = "Default";
            }
            if(!isset($groupApiMethods[$groupName])){
                $groupApiMethods[$groupName] = [];
            }


            $controllerMethodRefs = ReflectionCache::getInstance()->allowMethodReflections($reflection);
            /** @var \ReflectionMethod $controllerMethodRef */
            foreach ($controllerMethodRefs as $controllerMethodRef){
                $apiTag = $controllerMethodRef->getAttributes(Api::class);
                if(!empty($apiTag)){
                    $tag =  $apiTag[0];
                    $tag = new Api(...$tag->getArguments());
                    $apiName = $tag->apiName;
                    if(!isset($groupApiMethods[$groupName][$apiName])){
                        $groupApiMethods[$groupName][$apiName] = $tag;
                    }else{
                        throw new Annotation("api name:{$apiName} is duplicate");
                    }
                }
            }
            if($description){
                if(empty($groupDetail[$groupName])){
                    $groupDetail[$groupName] = $description;
                }else{
                    throw new Annotation("can not reassign group description for group ".$groupName);
                }
            }else{
                $groupDetail[$groupName] = null;
            }
        }

        //构建Group目录导航
        $finalDoc .= "## Navigator";
        $finalDoc = self::buildLine($finalDoc);
        $groupIndex = 1;
        foreach ($groupDetail as $groupName => $des){
            $finalDoc .= "{$groupIndex}. {$groupName} \n";
            $allMethods = $groupApiMethods[$groupName];
            $methodCount = 1;
            /** @var Api $tag */
            foreach ($allMethods as $tag){
                $finalDoc .= "    {$methodCount}. {$tag->apiName} \n";
                $methodCount ++;
            }
            $groupIndex ++;
        }
        $finalDoc = self::buildLine($finalDoc,3);

        //导航栏分割线

        $finalDoc.= "---------- ";
        $finalDoc = self::buildLine($finalDoc);

        //构建分组
        foreach ($groupDetail as $groupName => $des){
            $finalDoc .= "## {$groupName}";
            $finalDoc = self::buildLine($finalDoc);
            $des = self::parseDescription($des);
            if(!empty($des)){
                $finalDoc .= "{$des}";
                $finalDoc = self::buildLine($finalDoc);
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

    private static function buildLine(string $content,int $repeat = 1):string
    {
        return $content .str_repeat("\n",$repeat);
    }

    private static function parseDescription(?Description $description):?string
    {
        if($description){
            return $description->desc;
        }
        return null;
    }
}