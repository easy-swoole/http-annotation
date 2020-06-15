<?php


namespace EasySwoole\HttpAnnotation\Annotation;


use EasySwoole\Annotation\Annotation;
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
            $annotation->addParserTag(new ApiFail());
            $annotation->addParserTag(new ApiGroup());
            $annotation->addParserTag(new ApiGroupAuth());
            $annotation->addParserTag(new ApiGroupDescription());
            $annotation->addParserTag(new ApiSuccess());
            $annotation->addParserTag(new ApiRequestExample());
            $annotation->addParserTag(new ApiResponseParam());
            $annotation->addAlias('ApiAuth','Param');
            $this->parser = $annotation;
        }
        return $this->parser;
    }

    function scanDir(string $path)
    {
        if(is_file($path)){
            $list = [$path];
        }else{
            $list = File::scanDirectory($path)['files'];
        }
        $ret = [];
        if(!empty($list)){
            $parser = $this->getAnnotationParser();
            foreach ($list as $file){
                $class = static::getFileDeclareClass($file);
                if($class){
                    $ret[] = $this->getClassAnnotation($class);
                }
            }
        }
        return $ret;
    }

    function getClassAnnotation(string $class):ClassAnnotation
    {
        $classAnnotation = new ClassAnnotation();
        $ref = new \ReflectionClass($class);
        $global = $this->getAnnotationParser()->getAnnotation($ref);
        foreach (['ApiGroup','ApiGroupAuth','ApiGroupDescription'] as $key){
            if(isset($global[$key])){
                $method = "set{$key}";
                $classAnnotation->$method($global[$key][0]);
            }
        }
        foreach ($ref->getMethods() as $method){
            $methodAnnotation = $classAnnotation->addMethod($method->getName());
            $methodAnnotation->setMethodReflection($method);
            $methodAnnotation->setAnnotation($this->getAnnotationParser()->getAnnotation($method));
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