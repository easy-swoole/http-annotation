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
use EasySwoole\HttpAnnotation\Annotation\Method as MethodAnnotation;
use EasySwoole\HttpAnnotation\AnnotationTag\Param;
use EasySwoole\HttpAnnotation\Exception\Annotation\InvalidTag;
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

    function scanDir(string $path):array
    {
        if(is_file($path)){
            $list = [$path];
        }else{
            $list = File::scanDirectory($path)['files'];
        }
        $objectsAnnotation = [];
        if(!empty($list)){
            foreach ($list as $file){
                $class = static::getFileDeclareClass($file);
                if($class){
                    $objectsAnnotation[] = $this->getObjectAnnotation($class);
                }
            }
        }
        return $this->mergeAnnotationGroup($objectsAnnotation);
    }

    function annotations2Html(array $list)
    {

    }


    function mergeAnnotationGroup(array $objectsAnnotation)
    {
        $group = [];
        foreach ($objectsAnnotation as $objectAnnotation){
            $apiGroup = 'default';
            if($objectAnnotation->getApiGroup()){
                $apiGroup = $objectAnnotation->getApiGroup()->groupName;
                if(!empty($group[$apiGroup]['apiGroupDescription'])){
                    throw new InvalidTag("your cannot redeclare ApiGroupDescription for group:{$apiGroup}");
                }else{
                    $group[$apiGroup] = [
                        'apiGroupDescription'=>$objectAnnotation->getApiGroupDescription(),
                    ];
                }
                foreach ($objectAnnotation->getApiGroupAuth() as $auth){
                    $group[$apiGroup]['auth'][$auth->name] = $auth;
                }
                $group[$apiGroup]['methods'] = [];
            }
            /** @var MethodAnnotation $method */
            foreach ($objectAnnotation->getMethods() as $methodName => $method){
                $apiName = null;
                $hasApiTag = false;
                $api = $method->getAnnotationTag('Api',0);
                if($api){
                    $apiName = $api->name;
                    $hasApiTag = true;
                }else{
                    $apiName = $methodName;
                }
                //设置了Api tag的时候，name值禁止相同
                if(isset($group[$apiGroup]['methods'][$apiName]) && $hasApiTag){
                    throw new InvalidTag("apiName {$apiName} for group {$group} is duplicate");
                }
                $group[$apiGroup]['methods'][$apiName] = $method;
            }
        }
        return $group;
    }

    function getObjectAnnotation(string $class, ?int $filterType = null):ObjectAnnotation
    {
        $object = new ObjectAnnotation();
        $ref = new \ReflectionClass($class);
        $object->setReflection($ref);
        $global = $this->getAnnotationParser()->getAnnotation($ref);
        foreach (['ApiGroup','ApiGroupDescription'] as $key){
            if(isset($global[$key])){
                $method = "set{$key}";
                $object->$method($global[$key][0]);
            }
        }
        if(isset($global['ApiGroupAuth'])){
            $object->setApiGroupAuth($global['ApiGroupAuth']);
        }
        foreach ($ref->getMethods($filterType) as $method){
            $temp = $this->getAnnotationParser()->getAnnotation($method);
            $methodAnnotation = $object->addMethod($method->getName());
            $methodAnnotation->setReflection($method);
            if(!empty($temp)){
                $methodAnnotation->setAnnotation($temp);
            }
        }

        foreach ($ref->getProperties($filterType) as $property){
            $p = $object->addProperty($property->getName());
            $p->setReflection($property);
            $temp = $this->getAnnotationParser()->getAnnotation($property);
            if(!empty($temp)){
                $p->setAnnotation($temp);
            }
        }

        return $object;
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