<?php


namespace EasySwoole\HttpAnnotation\Annotation;


use EasySwoole\Annotation\Annotation;
use EasySwoole\HttpAnnotation\AnnotationTag\ApiDescription;
use EasySwoole\HttpAnnotation\AnnotationTag\CircuitBreaker;
use EasySwoole\HttpAnnotation\AnnotationTag\Context;
use EasySwoole\HttpAnnotation\AnnotationTag\Di;
use EasySwoole\HttpAnnotation\AnnotationTag\Api;
use EasySwoole\HttpAnnotation\AnnotationTag\ApiAuth;
use EasySwoole\HttpAnnotation\AnnotationTag\ApiFail;
use EasySwoole\HttpAnnotation\AnnotationTag\ApiGroup;
use EasySwoole\HttpAnnotation\AnnotationTag\ApiGroupAuth;
use EasySwoole\HttpAnnotation\AnnotationTag\ApiGroupDescription;
use EasySwoole\HttpAnnotation\AnnotationTag\ApiRequestExample;
use EasySwoole\HttpAnnotation\AnnotationTag\ApiResponseParam;
use EasySwoole\HttpAnnotation\AnnotationTag\ApiSuccess;
use EasySwoole\HttpAnnotation\AnnotationTag\Method;
use EasySwoole\HttpAnnotation\AnnotationTag\Param;
use EasySwoole\Utility\File;

class Parser
{
    protected $parser;

    function __construct()
    {
        static::preDefines([
            "POST"=>"POST",
            "GET"=>"GET",
            'COOKIE'=>'COOKIE',
            'HEADER'=>'HEADER',
            'FILE'=>'FILE',
            'DI'=>'DI',
            'CONTEXT'=>'CONTEXT',
            'RAW'=>'RAW'
        ]);
    }

    public static function preDefines($defines = [])
    {
        foreach ($defines as $key => $val){
            if(!defined($key)){
                define($key,$val);
            }
        }
    }

    public function getAnnotationParser():Annotation
    {
        if(!$this->parser){
            $annotation = new Annotation();
            $annotation->addParserTag(new Method());
            $annotation->addParserTag(new Param());
            $annotation->addParserTag(new Context());
            $annotation->addParserTag(new Di());
            $annotation->addParserTag(new CircuitBreaker());
            $annotation->addParserTag(new Api());
            $annotation->addParserTag(new ApiAuth());
            $annotation->addParserTag(new ApiDescription());
            $annotation->addParserTag(new ApiFail());
            $annotation->addParserTag(new ApiGroup());
            $annotation->addParserTag(new ApiGroupAuth());
            $annotation->addParserTag(new ApiGroupDescription());
            $annotation->addParserTag(new ApiSuccess());
            $annotation->addParserTag(new ApiRequestExample());
            $annotation->addParserTag(new ApiResponseParam());
            $this->parser = $annotation;
        }
        return $this->parser;
    }

    function scanDir(string $path,bool $globalMerge = true):array
    {
        if(is_file($path)){
            $list = [$path];
        }else{
            $list = File::scanDirectory($path)['files'];
        }
        $ret = [];
        if(!empty($list)){
            foreach ($list as $file){
                $class = static::getFileDeclareClass($file);
                if($class){
                    $ret[] = $this->getClassAnnotation($class);
                }
            }
        }
        if($globalMerge){
            $group = [];
            //先找去全部的group信息并合并
            foreach ($ret as $classAnnotation){
                $classGroup = null;
                if($classAnnotation->getApiGroup()){
                    $classGroup = $classAnnotation->getApiGroup()->groupName;
                    $group[$classGroup] = [
                        'apiGroupDescription'=>$classAnnotation->getApiGroupDescription(),
                    ];
                    foreach ($classAnnotation->getApiGroupAuth() as $auth){
                        $group[$classGroup]['auth'][$auth->name] = $auth;
                    }
                    $group[$classGroup]['methods'] = [];
                }
                //找出方法注解内有没有定义group
                /** @var MethodAnnotation $method */
                foreach ($classAnnotation->getMethods() as $methodName => $method){
                    $currentGroup = null;
                    $api = $method->getAnnotationTag('Api',0);
                    $methodGroup = $method->getAnnotationTag('ApiGroup',0);
                    if($methodGroup){
                        $currentGroup = $methodGroup->groupName;
                    }else if($api && !empty($api->group)){
                        $currentGroup = $api->group;
                    }else{
                        $currentGroup = $classGroup;
                    }
                    if(!empty($currentGroup)){
                        $group[$classGroup]['methods'][$methodName] = $method;
                        if(!isset($group[$currentGroup])){
                            $group[$currentGroup] = [
                                'apiGroupDescription'=>$method->getAnnotationTag('ApiGroupDescription',0),
                            ];
                        }
                        if($method->getAnnotationTag('ApiGroupAuth')){
                            foreach ($method->getAnnotationTag('ApiGroupAuth') as $tag){
                                $group[$currentGroup][$tag->name] = $tag;
                            }
                        }
                    }
                }
            }
            return $group;
        }else{
            return $ret;
        }
    }

    function annotations2Html(array $list)
    {

    }


    function getClassAnnotation(string $class,?int $methodType = null):ClassAnnotation
    {
        $classAnnotation = new ClassAnnotation();
        $ref = new \ReflectionClass($class);
        $global = $this->getAnnotationParser()->getAnnotation($ref);
        foreach (['ApiGroup','ApiGroupDescription'] as $key){
            if(isset($global[$key])){
                $method = "set{$key}";
                $classAnnotation->$method($global[$key][0]);
            }
        }
        if(isset($global['ApiGroupAuth'])){
            $classAnnotation->setApiGroupAuth($global['ApiGroupAuth']);
        }
        foreach ($ref->getMethods($methodType) as $method){
            $temp = $this->getAnnotationParser()->getAnnotation($method);
            $methodAnnotation = $classAnnotation->addMethod($method->getName());
            $methodAnnotation->setMethodReflection($method);
            if(!empty($temp)){
                $methodAnnotation->setAnnotation($temp);
            }
        }
        return $classAnnotation;
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