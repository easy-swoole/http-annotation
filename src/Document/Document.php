<?php

namespace EasySwoole\HttpAnnotation\Document;

use EasySwoole\Http\ReflectionCache;
use EasySwoole\HttpAnnotation\AnnotationController;
use EasySwoole\HttpAnnotation\AttributeCache;
use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\ApiGroup;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Bean\ClassAttribute;
use EasySwoole\HttpAnnotation\Enum\HttpMethod;
use EasySwoole\HttpAnnotation\Enum\ParamFrom;
use EasySwoole\HttpAnnotation\Enum\ParamType;
use EasySwoole\HttpAnnotation\Exception\Annotation;
use EasySwoole\HttpAnnotation\Utility;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use EasySwoole\ParserDown\ParserDown;
use EasySwoole\Spl\SplArray;
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

    function scanToHtml():void
    {
        $map = $this->scan();
        $documentMap = [];

        foreach ($map as $apiGroupName => $apiGroupInfo){
            $apiGroupNamePaths = explode('.', $apiGroupName);
            $temp = &$documentMap;

            foreach ($apiGroupNamePaths as $apiGroupNamePath){
                if(!isset($temp[$apiGroupNamePath])){
                    $temp[$apiGroupNamePath] = [
                        'apis'=>[],
                        'children'=>[],
                        'apiGroupName'=>$apiGroupName,
                        'description'=>null,
                        'onRequestParams'=>[]
                    ];
                }
                $currentGroup = &$temp[$apiGroupNamePath];
                $temp = &$currentGroup['children'];
            }

            /** @var ApiGroup $apiGroup */
            $apiGroup = $apiGroupInfo['apiGroup'];
            /** @var ClassAttribute $classAttribute */
            $classAttribute = $apiGroupInfo['classAttribute'];
            if($apiGroup->description){
                $currentGroup['description'] = $apiGroup->description->toString();
            }
            $onRequestParams = $classAttribute->onRequest->onRequestParams;
            /** @var Param $onRequestParam */
            foreach ($onRequestParams as $onRequestParam){
                $currentGroup['onRequestParams'][$onRequestParam->name] = [
                    'rule'=>'',
                    'type'=>$onRequestParam->type,
                    'description'=>$onRequestParam->description ? $onRequestParam->description->toString() : null,
                ];
                $validateRules = [];
                /** @var AbstractValidator $validateRule */
                foreach ($onRequestParam->validate as $validateRule){
                    $validateRules[$validateRule->ruleName()] = $validateRule->errorMsg($onRequestParam->name);
                }
                $currentGroup['onRequestParams'][$onRequestParam->name]['validateRules'] = $validateRules;
            }
//            $currentGroup['apis'][] = $apiGroup;
            // 每次外层循环结束，清理临时引用避免污染
            unset($currentGroup);
            unset($temp);
        }

        var_dump($documentMap);
    }
}