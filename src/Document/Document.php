<?php

namespace EasySwoole\HttpAnnotation\Document;

use EasySwoole\Http\ReflectionCache;
use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\ApiGroup;
use EasySwoole\HttpAnnotation\Attributes\Description;
use EasySwoole\HttpAnnotation\Attributes\Example;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Attributes\Validator\AbstractValidator;
use EasySwoole\HttpAnnotation\Enum\HttpMethod;
use EasySwoole\HttpAnnotation\Enum\ParamFrom;
use EasySwoole\HttpAnnotation\Enum\ParamType;
use EasySwoole\HttpAnnotation\Exception\Annotation;
use EasySwoole\HttpAnnotation\Utility;
use EasySwoole\ParserDown\ParserDown;

class Document
{
    public function __construct(
        private string $controllerPath,
        private string $controllerNameSpace = 'App\HttpController'
    )
    {
        if(!(is_file($controllerPath) || is_dir($controllerPath))){
            throw new Annotation("{$controllerPath} not exist");
        }
    }

    function scan()
    {
        $list = [];
        $len = strlen($this->controllerNameSpace);
        $allControllerClass = Utility::scanAllController($this->controllerPath);
        foreach ($allControllerClass as $controllerClass){
            if(substr($controllerClass,0,$len) !== $this->controllerNameSpace){
                throw new Annotation("class {$controllerClass} namespace not complete with {$this->controllerNameSpace}");
            }
            $ref = ReflectionCache::getInstance()->getClassReflection($controllerClass);
            //判断分组
            $g = $ref->getAttributes(ApiGroup::class);
            if(!empty($g)){
                $g = new ApiGroup(...$g[0]->getArguments());
            }else{
                $g = new ApiGroup("Default");
            }
            if(!isset($list[$g->groupName])){
                $list[$g->groupName] = new Group($g->groupName,$g->description);
            }else{
                if($g->description){
                    /** @var Group $group */
                    $group = $list[$g->groupName];
                    if($group->getDescription() == null){
                        $group->setDescription($g->description);
                    }else{
                        throw new Annotation("ApiGroup {$group->getName()} cannot rewrite description twice");
                    }
                }
            }
            /** @var Group $group */
            $group = $list[$g->groupName];

            $methods = ReflectionCache::getInstance()->allowMethodReflections($ref);

            //用于构建控制器路径
            $trimClass = ltrim(str_replace($this->controllerNameSpace,"",$controllerClass),"\\");
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



            /**
             * @var  $name
             * @var \ReflectionMethod $method
             */
            foreach ($methods as $name => $method){
                $api = $method->getAttributes(Api::class);
                if(!empty($api)){
                    $api = new Api(...$api[0]->getArguments());
                    $api->requestParam = Utility::parseActionParams($ref,$name);

                    if(empty($api->requestPath)){
                        $api->requestPath = "/{$controllerRequestPrefix}/{$method->name}";
                    }

                    if(!$group->addApi($api)){
                        throw new Annotation("cannot redefine apiName {$api->apiName} in apiGroup {$group->getName()}");
                    }
                }
            }
        }
        return $list;
    }

    function scanToHtml(string $projectName = "EasySwoole")
    {
        $str = self::scanToMarkdown();
        $temp = file_get_contents(__DIR__ . '/doc.tpl');
        $html = ParserDown::instance()->parse($str);
        $temp = str_replace('{{$apiDoc}}',$html,$temp);
        return str_replace('{{$projectName}}',$projectName,$temp);
    }

    function scanToMarkdown(): string
    {
        $groupDetail = $this->scan();
        //构建Group目录导航
        $finalDoc = "";
        $finalDoc .= "<h1 id='Navigator'>Navigator</h1>";
        $finalDoc = $this->buildLine($finalDoc);
        $finalDoc = $this->buildLine($finalDoc);
        /**
         * @var  $groupName
         * @var Group $group
         */
        foreach ($groupDetail as $groupName => $group){
            //删除无实际方法分组
            if(empty($group->getApis())){
                unset($groupDetail[$groupName]);
                continue;
            }

            $finalDoc .= "### [{$groupName}](#{$groupName}) ";
            $finalDoc = $this->buildLine($finalDoc,2);
            $allMethods = $group->getApis();
            $methodCount = 1;
            /** @var Api $tag */
            foreach ($allMethods as $tag){
                $finalDoc .= "{$methodCount}. [{$tag->apiName}](#{$groupName}-{$tag->apiName}) \n";
                $methodCount ++;
            }
            $finalDoc = $this->buildLine($finalDoc,2);
        }
        $finalDoc = $this->buildLine($finalDoc,3);

        //导航栏分割线
        $finalDoc = $this->buildLine($finalDoc);
        $finalDoc.= "---------- ";
        $finalDoc = $this->buildLine($finalDoc);

        $finalDoc .= "# Api List";
        $finalDoc = $this->buildLine($finalDoc);
        

        //构建分组详情
        foreach ($groupDetail as $groupName => $group){
            $apiMethods = $group->getApis();
            $finalDoc .= "<h2 id=\"{$groupName}\">{$groupName}</h2>";
            $finalDoc = $this->buildLine($finalDoc);
            //构建组说明
            $des = $this->parseDescription($group->getDescription());
            if(!empty($des)){
                $finalDoc = $this->buildLine($finalDoc);
                $finalDoc .= "{$des}";
                $finalDoc = $this->buildLine($finalDoc);
                $finalDoc = $this->buildLine($finalDoc);

            }

            //文档方法表
            /** @var Api $apiTag */
            foreach ($apiMethods as $apiTag){
                $finalDoc .= "<h3 id=\"{$groupName}-{$apiTag->apiName}\">{$apiTag->apiName} <sup>{$groupName}</sup></h3>";
                $finalDoc = $this->buildLine($finalDoc);
                $finalDoc = $this->buildLine($finalDoc);
                $finalDoc .= "**Request Path:** {$apiTag->requestPath}";
                $finalDoc = $this->buildLine($finalDoc);
                $finalDoc = $this->buildLine($finalDoc);
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
                $finalDoc = $this->buildLine($finalDoc);
                $finalDoc = $this->buildLine($finalDoc);
                $finalDoc .= "**Api Description:**";
                $finalDoc = $this->buildLine($finalDoc);
                $finalDoc = $this->buildLine($finalDoc);

                //说明
                $des = $this->parseDescription($apiTag->description);
                if(!empty($des)){
                    $finalDoc = $this->buildLine($finalDoc);
                    $finalDoc .= "{$des}";
                    $finalDoc = $this->buildLine($finalDoc);
                }else{
                    $finalDoc .= "Empty Method Description";
                    $finalDoc = $this->buildLine($finalDoc);
                }
                $finalDoc = $this->buildLine($finalDoc);
                //请求参数
                $finalDoc .= "**Request Params:**";
                $finalDoc = $this->buildLine($finalDoc);
                $finalDoc = $this->buildLine($finalDoc);
                $params = $apiTag->requestParam;
                if(!empty($params)){
                    $finalDoc .= $this->buildRequestParamsTable($params);
                }else{
                    $finalDoc .= "No Any Request Params Defined";
                }
                $finalDoc = $this->buildLine($finalDoc);
                $finalDoc = $this->buildLine($finalDoc);


                //响应
                $finalDoc .= "**Response Params:**";
                $finalDoc = $this->buildLine($finalDoc);
                $finalDoc = $this->buildLine($finalDoc);
                $params = $apiTag->responseParam;
                if(!empty($params)){
                    $finalDoc .= $this->buildExampleParamsTable($params);
                }else{
                    $finalDoc .= "No Any Response Params Defined";
                }
                $finalDoc = $this->buildLine($finalDoc);
                $finalDoc = $this->buildLine($finalDoc);

                //请求参数示例
                $requestExamples = $apiTag->requestExample;
                if(!empty($requestExamples)){
                    $count = 1;
                    /** @var Example $example */
                    foreach ($requestExamples as $example){
                        $finalDoc .= "**Request Example{$count}:**";
                        $finalDoc = $this->buildLine($finalDoc);
                        if($example->params){
                            $finalDoc .= $this->buildExampleParamsTable($example->params);
                            $finalDoc = $this->buildLine($finalDoc);
                            $finalDoc = $this->buildLine($finalDoc);
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
                            $finalDoc = $this->buildLine($finalDoc);
                            $finalDoc = $this->buildLine($finalDoc);
                            $finalDoc .= $this->parseDescription($example->description);
                            $finalDoc = $this->buildLine($finalDoc);
                            $finalDoc = $this->buildLine($finalDoc);
                        }
                        $count++;
                    }
                }else{
                    $finalDoc .= "**Request Example:**";
                    $finalDoc = $this->buildLine($finalDoc);
                    $finalDoc = $this->buildLine($finalDoc);
                    $finalDoc .= "No Example Request";
                }
                $finalDoc = $this->buildLine($finalDoc);
                $finalDoc = $this->buildLine($finalDoc);

                //响应示例
                $responseParams = $apiTag->responseExample;
                if(!empty($responseParams)){
                    $count = 1;
                    /** @var Example $example */
                    foreach ($responseParams as $example){
                        $finalDoc .= "**Response Example{$count}:**";
                        $finalDoc = $this->buildLine($finalDoc);
                        if($example->params){
                            $finalDoc .= $this->buildExampleParamsTable($example->params);
                            $finalDoc = $this->buildLine($finalDoc);
                            $finalDoc = $this->buildLine($finalDoc);
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
                            $finalDoc = $this->buildLine($finalDoc);
                            $finalDoc = $this->buildLine($finalDoc);

                            $finalDoc .= $this->parseDescription($example->description);
                            $finalDoc = $this->buildLine($finalDoc);
                            $finalDoc = $this->buildLine($finalDoc);
                        }
                        $count++;
                    }
                }else{
                    $finalDoc .= "**Response Example:**";
                    $finalDoc = $this->buildLine($finalDoc);
                    $finalDoc = $this->buildLine($finalDoc);
                    $finalDoc .= "No Response Example";
                }
                $finalDoc = $this->buildLine($finalDoc);
                $finalDoc = $this->buildLine($finalDoc);
            }


            $finalDoc = $this->buildLine($finalDoc);
            $finalDoc.= "---------- ";
            $finalDoc = $this->buildLine($finalDoc);
        }


        return $finalDoc;
    }

    private function buildLine(string $content,int $repeat = 1):string
    {
        return $content .str_repeat("\n",$repeat);
    }

    private function parseDescription(Description|string|null $description = null):?string
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


    private function buildRequestParamsTable(array $array):string
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
            $temp = $this->buildLine($temp);
            $temp = $this->buildLine($temp);
            $rules = $item->validate;
            /** @var AbstractValidator $rule */
            $count = 1;
            foreach ($rules as $rule){
                $rule->setCurrentParam($item);
                $temp .= "{$count}. {$rule->errorMsg()} <br>";
                $temp = $this->buildLine($temp);
                $temp = $this->buildLine($temp);
                $count++;
            }
            $validate = $temp;


            $desc = $this->parseDescription($item->description);

            $default = $this->valueHandler($item);

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


    private function valueHandler(Param $param):?string
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

    private function buildExampleParamsTable(array $array):string
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
            $desc->nodeValue = $this->parseDescription($item->description);
            $line->appendChild($desc);

            $default = $dom->createElement("td");
            $default->nodeValue = $this->valueHandler($item);
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
}