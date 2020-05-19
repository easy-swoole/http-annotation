<?php


namespace EasySwoole\HttpAnnotation\DocGenerator;


use EasySwoole\Annotation\Annotation;
use EasySwoole\Http\UrlParser;
use EasySwoole\HttpAnnotation\AnnotationTag\CircuitBreaker;
use EasySwoole\HttpAnnotation\AnnotationTag\Context;
use EasySwoole\HttpAnnotation\AnnotationTag\Di;
use EasySwoole\HttpAnnotation\AnnotationTag\DocTag\Api;
use EasySwoole\HttpAnnotation\AnnotationTag\DocTag\ApiFail;
use EasySwoole\HttpAnnotation\AnnotationTag\DocTag\ApiRequestExample;
use EasySwoole\HttpAnnotation\AnnotationTag\DocTag\ApiSuccess;
use EasySwoole\HttpAnnotation\AnnotationTag\DocTag\ResponseParam;
use EasySwoole\HttpAnnotation\AnnotationTag\Method;
use EasySwoole\HttpAnnotation\AnnotationTag\Param;
use EasySwoole\ParserDown\ParserDown;
use EasySwoole\Utility\File;
use FastRoute\RouteCollector;

class Utility
{
    public static function annotationToMarkdown(array $methodAnnotation):?string
    {
        //一定要有Api标签
        if(!isset($methodAnnotation['Api'][0])){
            return null;
        }
        /** @var Api $api */
        $api = $methodAnnotation['Api'][0];
        $tpl = '';
        $deprecated = '';
        if($api->deprecated){
            $deprecated = "<sup class='deprecated'>已废弃</sup>";
        }
        $tpl .= "<h2 id='{$api->group}-{$api->name}'>{$api->group}-{$api->name}{$deprecated}</h2>\n";
        $tpl .= "接口说明: <span>{$api->description}</span> \n\n";
        if(isset($methodAnnotation['Method'][0])){
            $method = implode("|",$methodAnnotation['Method'][0]->allow);
        }else{
            $method = '不限制';
        }
        $tpl .= "#### <span class='requestMethod'>Method : ```{$method}```</span> \n\n";
        $tpl .= "#### Url : <span>```{$api->path}```</span>\n\n";

        $tpl .= "### 请求 \n\n";

        if(isset($methodAnnotation['Param'])){
            $tpl .= "#### 请求字段 \n\n";
            $tpl .= "|字段|类型|描述|验证规则|\n";
            $tpl .= "|----|----|----|----|\n";
            /** @var Param $param */
            foreach ($methodAnnotation['Param'] as $param){
                $rule = json_encode($param->validateRuleList,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                $tpl .= "| {$param->name} | {$param->type} | {$param->description} | {$rule} |\n";
            }
            $tpl .= "\n\n";
        }

        if(isset($methodAnnotation['ApiRequestExample'])){
            $tpl .= "#### 请求示例 \n\n";
            $index = 1;
            foreach ($methodAnnotation['ApiRequestExample'] as $example){
                $content = static::contentFormat($example->getContent());
                $tpl .= "##### 请求示例{$index} \n\n";
                $tpl .= "```\n{$content}\n```\n";
            }
        }

        $tpl .= "### 响应 \n\n";

        if(isset($methodAnnotation['ResponseParam'])){
            $tpl .= "#### 响应字段 \n\n";
            $tpl .= "|字段|类型|描述|\n";
            $tpl .= "|----|----|----|\n";
            /** @var Param $param */
            foreach ($methodAnnotation['ResponseParam'] as $param){
                $tpl .= "| {$param->name} | {$param->type} | {$param->description} | \n";
            }
            $tpl .= "\n\n";
        }

        if(isset($methodAnnotation['ApiSuccess'])){
            $tpl .= "#### 成功响应示例 \n\n";
            $index = 1;
            foreach ($methodAnnotation['ApiSuccess'] as $example){
                $content = static::contentFormat($example->getContent());
                $tpl .= "##### 成功响应示例{$index} \n\n";
                $tpl .= "```\n{$content}\n```\n";
            }
        }

        if(isset($methodAnnotation['ApiFail'])){
            $tpl .= "#### 失败响应示例 \n\n";
            $index = 1;
            foreach ($methodAnnotation['ApiFail'] as $example){
                $content = static::contentFormat($example->getContent());
                $tpl .= "##### 失败响应示例{$index} \n\n";
                $tpl .= "```\n{$content} \n```\n";
            }
        }
        return $tpl;
    }

    public static function annotationsToHtml(array $methodAnnotations, ?string $extraMd = null)
    {
        $category = [];
        $temp = '';
        foreach ($methodAnnotations as $methodAnnotation){
            $ret = static::annotationToMarkdown($methodAnnotation);
            if($ret){
                $category[$methodAnnotation['Api'][0]->group][] = "{$methodAnnotation['Api'][0]->group}-{$methodAnnotation['Api'][0]->name}";
                $temp .= $ret;
            }
        }

        /*
         * 构造sidebar
         */
        $sideBar = '';

        if(!empty($extraMd)){
            $sideBar .= "- [附加说明](#附加说明)\n";
            $extraMd = "<h1 id='附加说明'>附加说明</h1>\n\n".$extraMd;
            $temp = $extraMd."\n\n".$temp;
        }

        foreach ($category as $group => $value){
            $sideBar .= "- {$group}\n";
            foreach ($value as $h){
                $sideBar .= "\t- [{$h}](#{$h})\n";
            }
        }

        $path = pathinfo(__FILE__)['dirname'];
        $tpl = file_get_contents($path.'/page.tpl');
        $parser = new ParserDown();
        $ret = $parser->text($sideBar);
        $tpl = str_replace('{$SIDEBAR}',$ret,$tpl);
        $ret = $parser->text($temp);
        return str_replace('{$BODY}',$ret,$tpl);
    }


    public static function renderClass(string $class):string
    {

        $parser = static::getAnnotation();
        $ref = new \ReflectionClass($class);
        $ret= [];
        foreach ($ref->getMethods() as $method){
            $temp = $parser->getAnnotation($method);
            if(!empty($temp)){
                $ret[] = $temp;
            }
        }
        return static::annotationsToHtml($ret);
    }

    public static function renderDir($path,?string $extraMd = null):?string
    {
        $temp = [];
        $ret = static::getPathAllAnnotations($path);
        foreach ($ret as $key => $list){
            foreach ($list as $item){
                $temp[] = $item;
            }
        }
        return static::annotationsToHtml($temp,$extraMd);
    }

    public static function mappingRouter(RouteCollector $collector,string $controllerPath,string $controllerNameSpace = 'App\\HttpController\\'):void
    {
        //用于psr规范去除命名空间
        $prefixLen = strlen(trim($controllerNameSpace,'\\'));
        $annotations = static::getPathAllAnnotations($controllerPath);
        foreach ($annotations as $class => $classAnnotation){
            foreach ($classAnnotation as $methodName => $annotation){
                if(isset($annotation['Api'][0]) && !empty($annotation['Api'][0]->path)){
                    /** @var Api $api */
                    $api = $annotation['Api'][0];
                    if(isset($annotation['Method'][0])){
                        $method = $annotation['Method'][0]->allow;
                    }else{
                        $method = ['POST','GET','PUT','PATCH','DELETE','HEAD','OPTIONS'];
                    }
                    $realPath = '/'.substr($class,$prefixLen + 1).'/'.$methodName;
                    $collector->addRoute($method,UrlParser::pathInfo($api->path),$realPath);
                }
            }
        }
    }

    public static function getPathAllAnnotations(string $path):array
    {
        if(is_file($path)){
            $list = [$path];
        }else{
            $list = File::scanDirectory($path)['files'];
        }
        $ret = [];
        $parser = static::getAnnotation();
        if(!empty($list)){
            foreach ($list as $file){
                $class = static::getFileDeclareClass($file);
                if($class){
                    $ref = new \ReflectionClass($class);
                    foreach ($ref->getMethods() as $method){
                        $temp = $parser->getAnnotation($method);
                        if(!empty($temp)){
                            $ret[$class][$method->getName()] = $temp;
                        }
                    }
                }
            }
        }
        return $ret;
    }

    public static function getFileDeclareClass(string $file):?string
    {
        $namespace = '';
        $class = NULL;
        $phpCode = file_get_contents($file);
        $tokens = token_get_all($phpCode);
        for ($i=0;$i<count($tokens);$i++) {
            if ($tokens[$i][0] === T_NAMESPACE) {
                for ($j=$i+1;$j<count($tokens); $j++) {
                    if ($tokens[$j][0] === T_STRING) {
                        $namespace .= '\\'.$tokens[$j][1];
                    } else if ($tokens[$j] === '{' || $tokens[$j] === ';') {
                        break;
                    }
                }
            }

            if ($tokens[$i][0] === T_CLASS) {
                for ($j=$i+1;$j<count($tokens);$j++) {
                    if ($tokens[$j] === '{') {
                        $class = $tokens[$i+2][1];
                    }
                }
            }
        }
        if(!empty($class)){
            if(!empty($namespace)){
                //去除第一个\
                $namespace = substr($namespace,1);
            }
            return $namespace.'\\'.$class;
        }else{
            return null;
        }
    }

    protected static function getAnnotation():Annotation
    {
        $annotation = new Annotation();
        /*
            * 注册解析命令
        */
        $annotation->addParserTag(new Method());
        $annotation->addParserTag(new Param());
        $annotation->addParserTag(new Context());
        $annotation->addParserTag(new Di());
        $annotation->addParserTag(new CircuitBreaker());
        $annotation->addParserTag(new Api());
        $annotation->addParserTag(new ApiFail());
        $annotation->addParserTag(new ApiSuccess());
        $annotation->addParserTag(new ApiRequestExample());
        $annotation->addParserTag(new ResponseParam());
        return $annotation;
    }

    protected static function contentFormat(string $content)
    {
        $json = json_decode($content,true);
        if($json){
            $content = json_encode($json,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
        }else{
            libxml_disable_entity_loader(true);
            $xml = simplexml_load_string($content, 'SimpleXMLElement', LIBXML_NOERROR | LIBXML_NOCDATA);
            if($xml){
                $content = $xml->saveXML();
            }
        }
        return $content;
    }
}