<?php

namespace EasySwoole\HttpAnnotation;

use EasySwoole\Http\ReflectionCache;
use EasySwoole\Http\UrlParser;
use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\ApiGroup;
use EasySwoole\HttpAnnotation\Attributes\Description;
use EasySwoole\HttpAnnotation\Attributes\Example;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Attributes\Validator\AbstractValidator;
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
                    $realPath = "/{$controllerPrefix}/{$methodRef->name}";
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
                    try{
                        $tag = new Api(...$tag->getArguments());
                    }catch (\Throwable $exception){
                        $msg = "{$exception->getMessage()} in controller: {$controller} method: {$controllerMethodRef->name}";
                        throw new Annotation($msg);
                    }

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
        $finalDoc .= "# Navigator";
        $finalDoc = self::buildLine($finalDoc);
        $groupIndex = 1;
        foreach ($groupDetail as $groupName => $des){
            $finalDoc .= "{$groupIndex}. [{$groupName}](#{$groupName}) \n";
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
        $finalDoc = self::buildLine($finalDoc);
        $finalDoc.= "---------- ";
        $finalDoc = self::buildLine($finalDoc);

        $finalDoc .= "# Api List";
        $finalDoc = self::buildLine($finalDoc);


        //构建分组详情
        foreach ($groupDetail as $groupName => $des){
            $apiMethods = $groupApiMethods[$groupName];
            $finalDoc .= "## {$groupName}";
            $finalDoc = self::buildLine($finalDoc);
            //构建组说明
            $des = self::parseDescription($des);
            if(!empty($des)){
                $finalDoc = self::buildLine($finalDoc);
                $finalDoc .= "{$des}";
                $finalDoc = self::buildLine($finalDoc);
                $finalDoc = self::buildLine($finalDoc);

            }

            if(!empty($apiMethods)){
                //文档方法表
                /** @var Api $apiTag */
                foreach ($apiMethods as $apiTag){
                    $finalDoc .= "### {$apiTag->apiName} <sup>{$groupName}</sup>";
                    $finalDoc = self::buildLine($finalDoc);

                    $finalDoc .= "**Request Path:** {$apiTag->requestPath}";
                    $finalDoc = self::buildLine($finalDoc);
                    $finalDoc = self::buildLine($finalDoc);
                    $temp = implode(",",$apiTag->allowMethod);
                    $finalDoc .= "**Allow Method:** $temp";
                    $finalDoc = self::buildLine($finalDoc);
                    $finalDoc = self::buildLine($finalDoc);
                    $finalDoc .= "**Api Description:**";
                    $finalDoc = self::buildLine($finalDoc);
                    $finalDoc = self::buildLine($finalDoc);

                    //说明
                    $des = self::parseDescription($apiTag->description);
                    if(!empty($des)){
                        $finalDoc = self::buildLine($finalDoc);
                        $finalDoc .= "{$des}";
                        $finalDoc = self::buildLine($finalDoc);
                    }else{
                        $finalDoc .= "empty method description";
                        $finalDoc = self::buildLine($finalDoc);
                    }
                    $finalDoc = self::buildLine($finalDoc);
                    //请求参数
                    $finalDoc .= "**Api Params:**";
                    $finalDoc = self::buildLine($finalDoc);
                    $finalDoc = self::buildLine($finalDoc);
                    $params = $apiTag->params;
                    if(!empty($params)){
                        $finalDoc .= self::buildParamsTable($params);
                    }else{
                        $finalDoc .= "no any params required";
                    }
                    $finalDoc = self::buildLine($finalDoc);
                    $finalDoc = self::buildLine($finalDoc);

                    //请求参数示例
                    $requestExamples = $apiTag->requestExample;
                    if(!empty($requestExamples)){
                        $count = 1;
                        /** @var Example $example */
                        foreach ($requestExamples as $example){
                            $finalDoc .= "**Request Example {$count}:**";
                            $finalDoc = self::buildLine($finalDoc);
                            if($example->params){
                                $finalDoc .= self::buildExampleParamsTable($example->params);
                                $finalDoc = self::buildLine($finalDoc);
                                $finalDoc = self::buildLine($finalDoc);
                            }
                            if($example->description){
                                $finalDoc .= self::parseDescription($example->description);
                                $finalDoc = self::buildLine($finalDoc);
                                $finalDoc = self::buildLine($finalDoc);
                            }
                            $count++;
                        }
                    }else{
                        $finalDoc .= "**Request Example:**";
                        $finalDoc = self::buildLine($finalDoc);
                        $finalDoc = self::buildLine($finalDoc);
                        $finalDoc .= "no example request";
                    }
                    $finalDoc = self::buildLine($finalDoc);
                    $finalDoc = self::buildLine($finalDoc);

                    //成功响应示例
                    $successExamples = $apiTag->successExample;
                    if(!empty($successExamples)){
                        $count = 1;
                        /** @var Example $example */
                        foreach ($successExamples as $example){
                            $finalDoc .= "**Success Response Example{$count}:**";
                            $finalDoc = self::buildLine($finalDoc);
                            if($example->params){
                                $finalDoc .= self::buildExampleParamsTable($example->params);
                                $finalDoc = self::buildLine($finalDoc);
                                $finalDoc = self::buildLine($finalDoc);
                            }
                            if($example->description){
                                $finalDoc .= self::parseDescription($example->description);
                                $finalDoc = self::buildLine($finalDoc);
                                $finalDoc = self::buildLine($finalDoc);
                            }
                            $count++;
                        }
                    }else{
                        $finalDoc .= "**Success Response Example:**";
                        $finalDoc = self::buildLine($finalDoc);
                        $finalDoc = self::buildLine($finalDoc);
                        $finalDoc .= "no success response example";
                    }
                    $finalDoc = self::buildLine($finalDoc);
                    $finalDoc = self::buildLine($finalDoc);


                    //失败响应示例
                    $successExamples = $apiTag->successExample;
                    if(!empty($successExamples)){
                        $count = 1;
                        /** @var Example $example */
                        foreach ($successExamples as $example){
                            $finalDoc .= "**Fail Response Example{$count}:**";
                            $finalDoc = self::buildLine($finalDoc);
                            if($example->params){
                                $finalDoc .= self::buildExampleParamsTable($example->params);
                                $finalDoc = self::buildLine($finalDoc);
                                $finalDoc = self::buildLine($finalDoc);
                            }
                            if($example->description){
                                $finalDoc .= self::parseDescription($example->description);
                                $finalDoc = self::buildLine($finalDoc);
                                $finalDoc = self::buildLine($finalDoc);
                            }
                            $count++;
                        }

                    }else{
                        $finalDoc .= "**Fail Response Example:**";
                        $finalDoc = self::buildLine($finalDoc);
                        $finalDoc = self::buildLine($finalDoc);
                        $finalDoc .= "no fail response example";
                    }
                    $finalDoc = self::buildLine($finalDoc);
                    $finalDoc = self::buildLine($finalDoc);







                }
            }else{
                $finalDoc.= "empty api method for group **{$groupName}**";
                $finalDoc = self::buildLine($finalDoc);
            }


            $finalDoc = self::buildLine($finalDoc);
            $finalDoc.= "---------- ";
            $finalDoc = self::buildLine($finalDoc);
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
            switch ($description->type){
                case Description::MARKDOWN:{
                    $file = $description->desc;
                    if(!is_file($file)){
                        throw new Annotation("markdown description file not exist :{$file}");
                    }
                    return file_get_contents($file);
                }
            }
        }
        return $description?->desc;
    }

    private static function buildParamsTable(array $array):string
    {

        $dom = new \DOMDocument();
        $dom->formatOutput = true;
        $root = $dom->createElement("table");
        //构建表头
        $header = $dom->createElement("tr");
        $list = ["Name","From","Validate","Description","Default"];
        foreach ($list as $item){
            $td = $dom->createElement("td");
            $td->nodeValue = $item;
            $header->appendChild($td);
        }
        $root->appendChild($header);

        //填充值
        /** @var Param $item */
        foreach ($array as $item){
            $line = $dom->createElement("tr");

            $name = $dom->createElement("td");
            $name->nodeValue = $item->name;
            $line->appendChild($name);

            $from = $dom->createElement("td");
            $from->nodeValue = implode(",",$item->from);
            $line->appendChild($from);

            $validate = $dom->createElement("td");
            $temp = "";
            $temp = self::buildLine($temp);
            $temp = self::buildLine($temp);
            $rules = $item->validate;
            /** @var AbstractValidator $rule */
            foreach ($rules as $rule){
                $rule->setParam($item);
                $temp .= "- {$rule->errorMsg()}";
                $temp = self::buildLine($temp);
            }
            $temp = self::buildLine($temp);
            $validate->nodeValue = $temp;
            $line->appendChild($validate);

            $desc = $dom->createElement("td");
            $desc->nodeValue = self::parseDescription($item->description);
            $line->appendChild($desc);

            $default = $dom->createElement("td");
            $default->nodeValue = $item->value;
            $line->appendChild($default);

            $root->appendChild($line);
        }

        return $dom->saveHTML($root);
    }


    private static function buildExampleParamsTable(array $array):string
    {

        $dom = new \DOMDocument();
        $dom->formatOutput = true;
        $root = $dom->createElement("table");
        //构建表头
        $header = $dom->createElement("tr");
        $list = ["Name","Description","Value"];
        foreach ($list as $item){
            $td = $dom->createElement("td");
            $td->nodeValue = $item;
            $header->appendChild($td);
        }
        $root->appendChild($header);

        //填充值
        /** @var Param $item */
        foreach ($array as $item){
            $line = $dom->createElement("tr");

            $name = $dom->createElement("td");
            $name->nodeValue = $item->name;
            $line->appendChild($name);

            $desc = $dom->createElement("td");
            $desc->nodeValue = self::parseDescription($item->description);
            $line->appendChild($desc);

            $default = $dom->createElement("td");
            $default->nodeValue = $item->value;
            $line->appendChild($default);

            $root->appendChild($line);
        }

        return $dom->saveHTML($root);
    }
}