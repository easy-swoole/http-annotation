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

        $this->controllerNameSpace = trim($this->controllerNameSpace, '\\');
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
        if(is_dir($this->controllerPath)){
            $files = File::scanDirectory($this->controllerPath)['files'];
        }else{
            $files = [$this->controllerPath];
        }
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
        $controllerNameSpaceLen = strlen($this->controllerNameSpace);

        foreach ($map as $apiGroupName => $apiGroupInfo){
            $apiGroupNamePaths = explode('.', $apiGroupName);
            $temp = &$documentMap;

            foreach ($apiGroupNamePaths as $apiGroupNamePath){
                if(!isset($temp[$apiGroupNamePath])){
                    $temp[$apiGroupNamePath] = [
                        'apiList'=>[],
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
            $buildParamsInfo = function (array $params): array
            {
                $result = [];
                /** @var Param $param */
                foreach ($params as $paramName => $param){
                    $result[$paramName] = [
                        'type'=>$param->type,
                        'description'=>$param->description ? $param->description->toString() : null,
                        'defaultValue'=>$param->value,
                        'ignoreAction'=>$param->ignoreAction,
                        'deprecated'=>$param->deprecated,
                    ];
                    $validateRules = [];
                    /** @var AbstractValidator $validateRule */
                    foreach ($param->validate as $validateRule){
                        $validateRules[$validateRule->ruleName()] = [
                            'msg'=>$validateRule->errorMsg($paramName),
                            'args'=>$validateRule->getRuleArgs()
                        ];
                    }
                    $result[$paramName]['validateRules'] = $validateRules;
                }
                return $result;
            };
            $onRequestParams = $classAttribute->onRequest->onRequestParams;
            $currentGroup['onRequestParams'] = $buildParamsInfo($onRequestParams);

            /**
             * @var  $apiName
             * @var Api $api
             */
            foreach ($classAttribute->apis as $apiName => $api){
                $path = substr($api->relateClass, $controllerNameSpaceLen);
                $path = str_replace('\\', '/', $path);
                $paths = explode('/', $path);
                $tailController = $paths[count($paths) - 1];
                if(strtolower($tailController) == 'index'){
                    array_pop($paths);
                }
                $paths = array_map('lcfirst', $paths);
                $api->requestPath = implode('/', $paths);
                if(strtolower($apiName) != 'index'){
                    $api->requestPath = "{$api->requestPath}/{$apiName}";
                }else{
                    $api->requestPath = "{$api->requestPath}/";
                }

                $currentGroup['apiList'][$apiName] = [
                    'apiName'=>$apiName,
                    'requestPath'=>$api->requestPath,
                    'requestParams'=>[],
                    'requestExamples'=>[],
                    'responseExamples'=>[
                        'success'=>[],
                        'fail'=>[]
                    ],
                    'description'=>$api->description ? $api->description->toString() : null,
                ];
                $currentGroup['apiList'][$apiName]['requestParams'] = $buildParamsInfo($api->requestParam);
                foreach ($api->requestExamples as $example) {
                    $currentGroup['apiList'][$apiName]['requestExamples'][] = $example->toString();
                }
                foreach ($api->responseExamples as $example) {
                    if($example->isSuccessResponse()){
                        $currentGroup['apiList'][$apiName]['responseExamples']['success'][] = $example->toString();
                    }else{
                        $currentGroup['apiList'][$apiName]['responseExamples']['fail'][] = $example->toString();
                    }
                }
            }

//            $currentGroup['apis'][] = $apiGroup;
            // 每次外层循环结束，清理临时引用避免污染
            unset($currentGroup);
            unset($temp);
        }

//        var_dump($documentMap);
    }
}