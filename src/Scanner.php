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
use EasySwoole\HttpAnnotation\Enum\HttpMethod;
use EasySwoole\HttpAnnotation\Enum\ParamType;
use EasySwoole\HttpAnnotation\Enum\ParamFrom;
use EasySwoole\HttpAnnotation\Exception\Annotation;
use EasySwoole\ParserDown\ParserDown;
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

    static function scanToHtml(string $controllerPath,string $projectName, string $controllerNameSpace = 'App\HttpController'):string
    {
        $str = self::scanToMarkdown($controllerPath,$controllerNameSpace);
        $temp = file_get_contents(__DIR__.'/doc.tpl');
        $html = ParserDown::instance()->parse($str);
        $temp = str_replace('{{$apiDoc}}',$html,$temp);
        return str_replace('{{$projectName}}',$projectName,$temp);
    }

    static function scanToMarkdown(string $controllerPath, string $controllerNameSpace = 'App\HttpController'):string{
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

            $controllerGlobalParams = [];
            $gTemp = $reflection->getAttributes(Param::class);
            foreach ($gTemp as $g){
                $args = $g->getArguments();
                try{
                    $controllerGlobalParams[] = new Param(...$args);
                }catch (\Throwable $exception){
                    $controller = static::class;
                    $msg = "{$exception->getMessage()} in controller: {$controller} global param";
                    throw new Annotation($msg);
                }
            }

            $onRequestParams = $reflection->getMethod("onRequest")->getAttributes(Param::class);
            $temp = [];
            foreach ($onRequestParams as $onRequestParam){
                $args = $onRequestParam->getArguments();
                try{
                    $onRequestParam = new Param(...$args);
                }catch (\Throwable $exception){
                    $controller = static::class;
                    $msg = "{$exception->getMessage()} in controller: {$controller} onRequest Method";
                    throw new Annotation($msg);
                }
                $temp[$onRequestParam->name] = $onRequestParam;
            }
            $onRequestParams = $temp;

            foreach ($controllerGlobalParams as $param){
                if(!isset($onRequestParams[$param->name])){
                    $onRequestParams[$param->name] = $param;
                }
            }

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

            /** @var \ReflectionMethod $controllerMethodRef */
            foreach ($controllerMethodRefs as $controllerMethodRef){
                $apiTag = $controllerMethodRef->getAttributes(Api::class);
                if(!empty($apiTag)){
                    $tag =  $apiTag[0];
                    try{
                        $tag = new Api(...$tag->getArguments());

                        if(empty($tag->requestPath)){
                            $tag->requestPath = "/{$controllerRequestPrefix}/{$controllerMethodRef->name}";
                        }

                        $tempArr = $tag->requestParam;

                        $tempOnRequestParams = $onRequestParams;
                        /**
                         * @var  $index
                         * @var Param $item
                         */
                        foreach ($tempArr as $index =>$item){
                            //参数覆盖检查
                            if(isset($tempOnRequestParams[$item->name])){
                                unset($tempOnRequestParams[$item->name]);
                            }
                        }
                        foreach ($tempOnRequestParams as $item){
                            if(!in_array($controllerMethodRef->name,$item->ignoreAction)){
                                $tempArr[] = $item;
                            }
                        }
                        $tag->requestParam = $tempArr;
                    }catch (\Throwable $exception){
                        $msg = "{$exception->getMessage()} in controller: {$controller} method: {$controllerMethodRef->name}";
                        throw new Annotation($msg);
                    }

                    $apiName = $tag->apiName;
                    if(!isset($groupApiMethods[$groupName][$apiName])){
                        $groupApiMethods[$groupName][$apiName] = $tag;
                    }else{
                        throw new Annotation("api name:{$apiName} is duplicate in api group:{$groupName}");
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
        $finalDoc .= "<h1 id='Navigator'>Navigator</h1>";
        $finalDoc = self::buildLine($finalDoc);
        $finalDoc = self::buildLine($finalDoc);
        foreach ($groupDetail as $groupName => $des){
            //删除无实际方法分组
            if(empty($groupApiMethods[$groupName])){
                unset($groupDetail[$groupName]);
                continue;
            }

            $finalDoc .= "### [{$groupName}](#{$groupName}) ";
            $finalDoc = self::buildLine($finalDoc,2);
            $allMethods = $groupApiMethods[$groupName];
            $methodCount = 1;
            /** @var Api $tag */
            foreach ($allMethods as $tag){
                $finalDoc .= "{$methodCount}. [{$tag->apiName}](#{$groupName}-{$tag->apiName}) \n";
                $methodCount ++;
            }
            $finalDoc = self::buildLine($finalDoc,2);
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
            $finalDoc .= "<h2 id=\"{$groupName}\">{$groupName}</h2>";
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
                    $finalDoc .= "<h3 id=\"{$groupName}-{$apiTag->apiName}\">{$apiTag->apiName} <sup>{$groupName}</sup></h3>";
                    $finalDoc = self::buildLine($finalDoc);
                    $finalDoc = self::buildLine($finalDoc);
                    $finalDoc .= "**Request Path:** {$apiTag->requestPath}";
                    $finalDoc = self::buildLine($finalDoc);
                    $finalDoc = self::buildLine($finalDoc);
                    $allMethodStr = "";
                    if($apiTag->allowMethod instanceof HttpMethod){
                        $allMethodStr = $apiTag->allowMethod->toString();
                    }else{
                        foreach ($apiTag->allowMethod as $allowMethodItem) {
                            $allMethodStr .= "{$allowMethodItem->toString()},";
                        }
                        $allMethodStr = rtrim($allMethodStr,',');
                    }
                    $finalDoc .= "**Allow Method:** {$allMethodStr}";
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
                        $finalDoc .= "Empty Method Description";
                        $finalDoc = self::buildLine($finalDoc);
                    }
                    $finalDoc = self::buildLine($finalDoc);
                    //请求参数
                    $finalDoc .= "**Request Params:**";
                    $finalDoc = self::buildLine($finalDoc);
                    $finalDoc = self::buildLine($finalDoc);
                    $params = $apiTag->requestParam;
                    if(!empty($params)){
                        $finalDoc .= self::buildRequestParamsTable($params);
                    }else{
                        $finalDoc .= "No Any Request Params Defined";
                    }
                    $finalDoc = self::buildLine($finalDoc);
                    $finalDoc = self::buildLine($finalDoc);


                    //响应
                    $finalDoc .= "**Response Params:**";
                    $finalDoc = self::buildLine($finalDoc);
                    $finalDoc = self::buildLine($finalDoc);
                    $params = $apiTag->responseParam;
                    if(!empty($params)){
                        $finalDoc .= self::buildExampleParamsTable($params);
                    }else{
                        $finalDoc .= "No Any Response Params Defined";
                    }
                    $finalDoc = self::buildLine($finalDoc);
                    $finalDoc = self::buildLine($finalDoc);

                    //请求参数示例
                    $requestExamples = $apiTag->requestExample;
                    if(!empty($requestExamples)){
                        $count = 1;
                        /** @var Example $example */
                        foreach ($requestExamples as $example){
                            $finalDoc .= "**Request Example{$count}:**";
                            $finalDoc = self::buildLine($finalDoc);
                            if($example->params){
                                $finalDoc .= self::buildExampleParamsTable($example->params);
                                $finalDoc = self::buildLine($finalDoc);
                                $finalDoc = self::buildLine($finalDoc);
                            }
                            if($example->description){
                                switch ($example->description->type){
                                    case Description::JSON:{
                                        $finalDoc .= "**Request Example{$count} JSON:**";
                                        break;
                                    }
                                    case Description::XML:{
                                        $finalDoc .= "**Request Example{$count} XML:**";
                                        break;
                                    }
                                    default:{
                                        $finalDoc .= "**Request Example{$count} Description:**";
                                    }
                                }
                                $finalDoc = self::buildLine($finalDoc);
                                $finalDoc = self::buildLine($finalDoc);
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
                        $finalDoc .= "No Example Request";
                    }
                    $finalDoc = self::buildLine($finalDoc);
                    $finalDoc = self::buildLine($finalDoc);

                    //响应示例
                    $responseParams = $apiTag->responseExample;
                    if(!empty($responseParams)){
                        $count = 1;
                        /** @var Example $example */
                        foreach ($responseParams as $example){
                            $finalDoc .= "**Response Example{$count}:**";
                            $finalDoc = self::buildLine($finalDoc);
                            if($example->params){
                                $finalDoc .= self::buildExampleParamsTable($example->params);
                                $finalDoc = self::buildLine($finalDoc);
                                $finalDoc = self::buildLine($finalDoc);
                            }
                            if($example->description){
                                switch ($example->description->type){
                                    case Description::JSON:{
                                        $finalDoc .= "**Response Example{$count} JSON:**";
                                        break;
                                    }
                                    case Description::XML:{
                                        $finalDoc .= "**Response Example{$count} XML:**";
                                        break;
                                    }
                                    default:{
                                        $finalDoc .= "**Response Example{$count} Description:**";
                                    }
                                }
                                $finalDoc = self::buildLine($finalDoc);
                                $finalDoc = self::buildLine($finalDoc);

                                $finalDoc .= self::parseDescription($example->description);
                                $finalDoc = self::buildLine($finalDoc);
                                $finalDoc = self::buildLine($finalDoc);
                            }
                            $count++;
                        }
                    }else{
                        $finalDoc .= "**Response Example:**";
                        $finalDoc = self::buildLine($finalDoc);
                        $finalDoc = self::buildLine($finalDoc);
                        $finalDoc .= "No Response Example";
                    }
                    $finalDoc = self::buildLine($finalDoc);
                    $finalDoc = self::buildLine($finalDoc);
                }
            }else{
                $finalDoc.= "Empty Api Method For Group **{$groupName}**";
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

    private static function parseDescription(Description|string|null $description = null):?string
    {
        if(is_string($description)){
            return  $description;
        }else if($description instanceof Description){
            switch ($description->type){
                case Description::MARKDOWN:{
                    $file = $description->desc;
                    if(!is_file($file)){
                        throw new Annotation("markdown description file not exist :{$file}");
                    }
                    return file_get_contents($file);
                }
                case Description::JSON:{
                    $file = $description->desc;
                    if(!is_file($file)){
                        throw new Annotation("json file not exist :{$file}");
                    }
                    $temp =  file_get_contents($file);
                    $temp = json_decode($temp);
                    if(!is_array($temp) && !is_object($temp)){
                        throw new Annotation("json file :{$file} not a valid format");
                    }
                    $temp = json_encode($temp, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                    return "```json\n$temp\n```";
                }
                case Description::XML:{
                    $file = $description->desc;
                    if(!is_file($file)){
                        throw new Annotation("xml file not exist :{$file}");
                    }
                    $temp =  file_get_contents($file);
                    $xml = simplexml_load_string($temp, 'SimpleXMLElement', LIBXML_NOERROR | LIBXML_NOCDATA);
                    if ($xml) {
                        $content = $xml->saveXML();
                        return "```xml\n$content\n```";
                    }
                    throw new Annotation("xml file :{$file} not a valid format");
                }
            }
        }
        return $description?->desc;
    }

    private static function buildExampleParamsTable(array $array):string
    {

        $dom = new \DOMDocument();
        $dom->formatOutput = true;
        $root = $dom->createElement("table");
        //构建表头
        $header = $dom->createElement("tr");
        $list = ["Name","Description","Default"];
        foreach ($list as $item){
            $td = $dom->createElement("td");
            $td->nodeValue = $item;
            $header->appendChild($td);
        }
        $root->appendChild($header);

        $builder = function (Param $item, $subCount = 0,ParamFrom|array $parentFrom = null)use($dom,$root,&$builder){
            if($parentFrom && ($parentFrom != $item->from)){
                throw new Annotation("param name: {$item->name} 'from' attribute is different with parent 'from' attribute : {$parentFrom->toString()}");
            }
            $line = $dom->createElement("tr");

            $name = $dom->createElement("td");
            $name->nodeValue = str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;",$subCount).$item->name;
            $line->appendChild($name);

            $desc = $dom->createElement("td");
            $desc->nodeValue = self::parseDescription($item->description);
            $line->appendChild($desc);

            $default = $dom->createElement("td");
            $default->nodeValue = self::valueHandler($item);
            $line->appendChild($default);

            $root->appendChild($line);

            //检查是否有下级

            $subCount++;
            foreach ($item->subObject as $sub){
                $builder($sub,$subCount,$item->from);
            }
        };

        //填充值
        /** @var Param $item */
        foreach ($array as $item){
            $builder($item);
        }

        return $dom->saveHTML($root);
    }

    private static function buildRequestParamsTable(array $array):string
    {
        //用模板构建是因为可能存在一些html 实体，不希望被转化

        $builder = function (Param $item, $subCount = 0,ParamFrom|array $parentFrom = null)use(&$builder){
            if($parentFrom && ($parentFrom != $item->from)){
                throw new Annotation("param name: {$item->name} 'from' attribute is different with parent 'from' attribute : {$parentFrom->toString()}");
            }

            $name = str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;",$subCount).$item->name;


            $fromStr = "";
            if($item->from instanceof ParamFrom){
                $fromStr = $item->from->toString();
            }else{
                foreach ($item->from as $fromItem) {
                    $fromStr .= "{$fromItem->toString()},";
                }
                $fromStr = rtrim($fromStr,',');
            }


            $temp = "";
            $temp = self::buildLine($temp);
            $temp = self::buildLine($temp);
            $rules = $item->validate;
            /** @var AbstractValidator $rule */
            $count = 1;
            foreach ($rules as $rule){
                $rule->setCurrentParam($item);
                $temp .= "{$count}. {$rule->errorMsg()} <br>";
                $temp = self::buildLine($temp);
                $temp = self::buildLine($temp);
                $count++;
            }
            $validate = $temp;


            $desc = self::parseDescription($item->description);

            $default = self::valueHandler($item);

            //检查是否有下级
            $next = "";
            $subCount++;
            foreach ($item->subObject as $sub){
                $next .= $builder($sub,$subCount,$item->from);
            }

            return "
<tr>
            <td>{$name}</td>
            <td>{$fromStr}</td>
            <td>{$validate}</td>
            <td>{$desc}</td>
            <td>{$default}</td>
		</tr>
		{$next}
		";
        };



        //填充值

        $final = "";

        /** @var Param $item */
        foreach ($array as $item){
            $final .= $builder($item);
        }

        return "
<table>
    <tr>
		<td>Name</td>
		<td>From</td>
		<td>Validate</td>
		<td>Description</td>
		<td>Default</td>
	</tr>
	{$final}
</table>";
    }


    public static function valueHandler(Param $param):?string
    {
        switch ($param->type){
            case ParamType::OBJECT:
            case ParamType::LIST:{
                if($param->value === null){
                    return null;
                }
                return json_encode($param->value,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
            }
            default:{
                if($param->value === null){
                    return null;
                }
                return (string)$param->value;
            }
        }
    }
}