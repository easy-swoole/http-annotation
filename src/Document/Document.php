<?php

namespace EasySwoole\HttpAnnotation\Document;

use EasySwoole\Http\ReflectionCache;
use EasySwoole\HttpAnnotation\AnnotationController;
use EasySwoole\HttpAnnotation\AttributeCache;
use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\ApiGroup;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Enum\HttpMethod;
use EasySwoole\HttpAnnotation\Enum\ParamFrom;
use EasySwoole\HttpAnnotation\Enum\ParamType;
use EasySwoole\HttpAnnotation\Exception\Annotation;
use EasySwoole\HttpAnnotation\Utility;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use EasySwoole\ParserDown\ParserDown;
use EasySwoole\Utility\File;
use ReflectionClass;

class Document
{
    private Config $config;
    public function __construct(
        private string $controllerPath,
        private string $controllerNameSpace = 'App\HttpController'
    )
    {
        if(!(is_file($controllerPath) || is_dir($controllerPath))){
            throw new Annotation("{$controllerPath} not exist");
        }
        $this->config = new Config();
    }

    public function getConfig():Config
    {
        return $this->config;
    }

    /**
     * 仅当定义了ApiGroup的控制器才会被扫描进去
     */
    function scan():array
    {
        $documentMap = [];
        $len = strlen($this->controllerNameSpace);
        $files = File::scanDirectory($this->controllerPath)['files'];
        foreach ($files as $file){
            $class = Utility::getFileDeclaredClass($file);
            if(empty($class)){
               continue;
            }
            $class = $class[0];
            $ref = new ReflectionClass($class);
            if(!$ref->isSubclassOf(AnnotationController::class)){
                continue;
            }
            $classAttribute = AttributeCache::getInstance()->parseClass($class);
            $apiGroup = $classAttribute->apiGroup;
            if(empty($apiGroup)){
                continue;
            }
            if(isset($documentMap[$apiGroup->groupName])){
                /** @var ApiGroup $old */
                $old = $documentMap[$apiGroup->groupName];
                throw new Annotation("apiGroupName {$apiGroup->groupName} is already defined in {$old->relateClass},redefine in {$apiGroup->relateClass} again");
            }

            $documentMap[$apiGroup->groupName] = [
                'apiGroup' => $apiGroup,
                'classAttribute'=>$classAttribute
            ];

        }
        return $documentMap;
    }

    function scanToHtml()
    {
        $json = json_encode($this->scan());
        $temp = file_get_contents(__DIR__ . '/doc.tpl');
        $temp = str_replace('{{$docData}}',$json,$temp);
        return str_replace('{{$config}}',json_encode($this->config),$temp);
    }
}