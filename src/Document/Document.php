<?php

namespace EasySwoole\HttpAnnotation\Document;

use EasySwoole\Http\ReflectionCache;
use EasySwoole\HttpAnnotation\AnnotationController;
use EasySwoole\HttpAnnotation\AttributeCache;
use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\ApiGroup;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Bean\ClassAttribute;
use EasySwoole\HttpAnnotation\Bean\Description\AbstractDescription;
use EasySwoole\HttpAnnotation\Bean\Description\Markdown;
use EasySwoole\HttpAnnotation\Exception\Annotation;
use EasySwoole\HttpAnnotation\Utility;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use EasySwoole\ParserDown\ParserDown;
use EasySwoole\Spl\SplArray;
use EasySwoole\Utility\File;
use ReflectionClass;
use EasySwoole\HttpAnnotation\Enum\ParamFrom;
use EasySwoole\HttpAnnotation\Enum\HttpMethod;

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
    function scanAllApiGroup():array
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

    function scan2ArrayMap():array
    {
        $map = $this->scanAllApiGroup();
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
                        'descriptionHtml'=>'',
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
                $currentGroup['descriptionHtml'] = $this->renderDescription($apiGroup->description);
            }
            $buildParamsInfo = function (array $params): array
            {
                $result = [];
                /** @var Param $param */
                foreach ($params as $paramName => $param){
                    $result[$paramName] = [
                        'type'=>$param->type?->name,
                        'from'=>$param->from?->name,
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
                    'deprecated'=>$api->deprecated,
                    'allowMethod'=>$api->allowMethod->name,
                    'acceptContentType'=>$api->acceptContentType?->name,
                    'requestPath'=>$api->requestPath,
                    'requestParams'=>[],
                    'requestExamples'=>[],
                    'responseExamples'=>[
                        'success'=>[],
                        'fail'=>[]
                    ],
                    'description'=>$api->description ? $api->description->toString() : null,
                    'descriptionHtml'=>$this->renderDescription($api->description),
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
            // 每次外层循环结束，清理临时引用避免污染
            unset($currentGroup);
            unset($temp);
        }

        return $documentMap;
    }

    /**
     * 返回完整的接口文档 HTML，由调用方输出或保存。
     */
    function scan2html(): string
    {
        $documentMap = $this->scan2ArrayMap();
        $escape = static fn(string $value): string => htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $jsonFlags = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
            | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE | JSON_THROW_ON_ERROR;
        // PHP 的无值枚举不能直接序列化为 JSON。
        $normalize = function (mixed $value) use (&$normalize): mixed {
            if ($value instanceof \UnitEnum) {
                return $value->name;
            }
            if (is_array($value)) {
                foreach ($value as $key => $item) {
                    $value[$key] = $normalize($item);
                }
            }
            return $value;
        };
        $buildMenu = function (array $infoMap, array $path = []) use (&$buildMenu, $escape, $jsonFlags): string {
            $html = '<ul>';
            foreach ($infoMap as $name => $info) {
                $groupPath = [...$path, (string)$name];
                $children = $buildMenu($info['children'], $groupPath);
                // 保留包含有效子菜单的父分组，只隐藏整棵子树都没有接口的节点。
                if (empty($info['apiList']) && $children === '<ul></ul>') {
                    continue;
                }
                $pathAttribute = $escape(json_encode($groupPath, $jsonFlags));
                $html .= '<li class="menu-group"><button type="button" class="group-toggle" aria-expanded="false" data-path="'
                    . $pathAttribute . '"><span class="menu-arrow" aria-hidden="true">▸</span> '
                    . $escape((string)$name) . '</button><ul hidden>';
                foreach (array_keys($info['apiList']) as $apiName) {
                    $html .= '<li><a href="#" data-path="' . $pathAttribute . '" data-api="'
                        . $escape((string)$apiName) . '">' . $escape((string)$apiName) . '</a></li>';
                }
                if (!empty($info['children'])) {
                    // 子分组的 li 直接嵌入当前分组的列表，支持任意层级。
                    $html .= substr($children, 4, -5);
                }
                $html .= '</ul></li>';
            }
            return $html . '</ul>';
        };
        $template = file_get_contents(__DIR__ . '/doc.tpl');
        if ($template === false) {
            throw new Annotation('Unable to read document template');
        }
        $descriptionHtml = $this->renderDescription($this->config->getDescription());
        return strtr($template, [
            '{{$introduction}}' => '<h1>' . $escape($this->config->getProjectName()) . '</h1>' . $descriptionHtml,
            '{{$sideBar}}' => $buildMenu($documentMap),
            '{{$docData}}' => json_encode($normalize($documentMap), $jsonFlags),
            '{{$config}}' => json_encode([
                'projectName' => $this->config->getProjectName(),
                'host' => $this->config->getHost(),
                'hasDescription' => $this->config->getDescription() !== null,
            ], $jsonFlags),
        ]);
    }
    private function renderDescription(?AbstractDescription $description): string
    {
        if ($description === null) {
            return '';
        }
        $text = $description->toString();
        if ($description instanceof Markdown) {
            return (new ParserDown())->text($text);
        }
        return '<pre>' . htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</pre>';
    }

}
