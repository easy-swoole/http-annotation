<?php


namespace EasySwoole\HttpAnnotation\Utility;


use EasySwoole\HttpAnnotation\Annotation\ObjectAnnotation;
use EasySwoole\HttpAnnotation\Annotation\Parser;
use EasySwoole\HttpAnnotation\Annotation\ParserInterface;
use FastRoute\RouteCollector;

class Scanner
{
    protected $parser;

    function __construct(ParserInterface $parser = null)
    {
        if(!$parser){
            $parser = new Parser();
        }
        $this->parser = $parser;
    }

    function getObjectAnnotation(string $class):ObjectAnnotation
    {
        $ref = new \ReflectionClass($class);
        return $this->parser->parseObject($ref);
    }

    function mappingRouter(RouteCollector $routeCollector,string $controllerPath,string $controllerNameSpace = 'App\\HttpController\\')
    {
        //用于psr规范去除命名空间
//        $prefixLen = strlen(trim($controllerNameSpace,'\\'));
//        $annotations = static::getPathAllAnnotations($controllerPath);
//        foreach ($annotations as $class => $classAnnotation){
//            foreach ($classAnnotation as $methodName => $annotation){
//                if(isset($annotation['Api'][0]) && !empty($annotation['Api'][0]->path)){
//                    /** @var Api $api */
//                    $api = $annotation['Api'][0];
//                    if(isset($annotation['Method'][0])){
//                        $method = $annotation['Method'][0]->allow;
//                    }else{
//                        $method = ['POST','GET','PUT','PATCH','DELETE','HEAD','OPTIONS'];
//                    }
//                    $realPath = '/'.substr($class,$prefixLen + 1).'/'.$methodName;
//                    $collector->addRoute($method,UrlParser::pathInfo($api->path),$realPath);
//                }
//            }
//        }
    }

    function scanAnnotations(string $pathOrFile):array
    {
        $ret = [];
        return $ret;
    }

    public static function getFileDeclaredClass(string $file):?string
    {
        $namespace = '';
        $class = NULL;
        $phpCode = file_get_contents($file);
        $tokens = token_get_all($phpCode);
        for ($i = 0; $i < count($tokens); $i++) {
            if ($tokens[$i][0] === T_NAMESPACE) {
                for ($j = $i + 1; $j < count($tokens); $j++) {
                    if ($tokens[$j][0] === T_STRING) {
                        $namespace .= '\\' . $tokens[$j][1];
                    } else if ($tokens[$j] === '{' || $tokens[$j] === ';') {
                        break;
                    }
                }
            }

            if ($tokens[$i][0] === T_CLASS) {
                for ($j = $i + 1; $j < count($tokens); $j++) {
                    if ($tokens[$j] === '{') {
                        $class = $tokens[$i + 2][1];
                        break;
                    }
                }
            }elseif ($class){
                break;
            }
        }
        if (!empty($class)) {
            if (!empty($namespace)) {
                //去除第一个\
                $namespace = substr($namespace, 1);
            }
            return $namespace . '\\' . $class;
        } else {
            return null;
        }
    }
}