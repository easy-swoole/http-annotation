<?php

namespace EasySwoole\HttpAnnotation\Document;

use EasySwoole\HttpAnnotation\AnnotationController;
use EasySwoole\HttpAnnotation\AttributeCache;
use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\ApiGroup;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Bean\ClassAttribute;
use EasySwoole\HttpAnnotation\Bean\Description\AbstractDescription;
use EasySwoole\HttpAnnotation\Bean\Description\Markdown;
use EasySwoole\HttpAnnotation\Bean\Example\ResParam;
use EasySwoole\HttpAnnotation\Exception\Annotation;
use EasySwoole\HttpAnnotation\Utility;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use EasySwoole\ParserDown\ParserDown;
use EasySwoole\Utility\File;
use ReflectionClass;
use UnitEnum;
use EasySwoole\HttpAnnotation\Enum\ParamFrom;

class Document
{
    private const JSON_FLAGS = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
        | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE | JSON_THROW_ON_ERROR;

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
                $old = $documentMap[$apiGroup->groupName]['apiGroup'];
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
            $onRequestParams = $classAttribute->onRequest->onRequestParams;
            $currentGroup['onRequestParams'] = $this->buildRequestParams($onRequestParams);

            /**
             * @var  $apiName
             * @var Api $api
             */
            foreach ($classAttribute->apis as $apiName => $api){
                $currentGroup['apiList'][$apiName] = $this->buildApiInfo($api, $apiName);
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
        $template = $this->readTemplate();
        $descriptionHtml = $this->renderDescription($this->config->getDescription());
        return strtr($template, [
            '{{$introduction}}' => '<h1>' . $this->escape($this->config->getProjectName()) . '</h1>' . $descriptionHtml,
            '{{$sideBar}}' => $this->buildMenu($documentMap),
            '{{$docData}}' => json_encode($this->normalizeJson($documentMap), self::JSON_FLAGS),
            '{{$config}}' => json_encode([
                'projectName' => $this->config->getProjectName(),
                'host' => $this->config->getHost(),
                'hasDescription' => $this->config->getDescription() !== null,
            ], self::JSON_FLAGS),
        ]);
    }

    // 请求参数与响应参数使用不同的数据结构，分别构建以避免字段含义混淆。
    private function buildRequestParams(array $params): array
    {
        $result = [];
        /** @var Param $param */
        foreach ($params as $paramName => $param){
            $result[$paramName] = [
                'type'=>$param->type?->name,
                'from'=>array_map(static fn(ParamFrom $source): string => $source->name, $param->from),
                'description'=>$param->description ? $param->description->toString() : null,
                'defaultValue'=>$param->value,
                'ignoreAction'=>$param->ignoreAction,
                'deprecated'=>$param->deprecated,
            ];
            $validateRules = [];
            /** @var AbstractValidator $validateRule */
            foreach ($param->validate as $validateRule){
                $validateRules[$validateRule::ruleName()] = [
                    'msg'=>$validateRule->errorMsg($paramName, $this->config->getValidateMsgMap()),
                    'args'=>$validateRule->getRuleArgs()
                ];
            }
            $result[$paramName]['validateRules'] = $validateRules;
        }
        return $result;
    }

    // 文档路径只参与输出，不回写 AttributeCache 中的 Api 定义。
    private function buildRequestPath(string $className, string $apiName): string
    {
        $path = substr($className, strlen($this->controllerNameSpace));
        $path = str_replace('\\', '/', $path);
        $paths = explode('/', $path);
        $tailController = $paths[count($paths) - 1];
        if (strtolower($tailController) == 'index') {
            array_pop($paths);
        }
        $paths = array_map('lcfirst', $paths);
        $requestPath = implode('/', $paths);
        if (strtolower($apiName) != 'index') {
            $requestPath = "{$requestPath}/{$apiName}";
        } else {
            $requestPath = "{$requestPath}/";
        }
        return $requestPath;
    }

    private function buildApiInfo(Api $api, string $apiName): array
    {
        $info = [
            'apiName'=>$apiName,
            'deprecated'=>$api->deprecated,
            'allowMethod'=>$api->allowMethod->name,
            'acceptContentType'=>$api->acceptContentType?->name,
            'requestPath'=>$this->buildRequestPath($api->relateClass, $apiName),
            'requestParams'=>[],
            'responseParams'=>[],
            'requestExamples'=>[],
            'responseExamples'=>[
                'success'=>[],
                'fail'=>[]
            ],
            'description'=>$api->description ? $api->description->toString() : null,
            'descriptionHtml'=>$this->renderDescription($api->description),
        ];
        $info['requestParams'] = $this->buildRequestParams($api->requestParam);
        $info['responseParams'] = $this->buildResponseParams($api, $apiName);
        foreach ($api->requestExamples as $example) {
            $info['requestExamples'][] = $example->toString();
        }
        foreach ($api->responseExamples as $example) {
            if($example->isSuccessResponse()){
                $info['responseExamples']['success'][] = $example->toString();
            }else{
                $info['responseExamples']['fail'][] = $example->toString();
            }
        }
        return $info;
    }

    private function buildResponseParams(Api $api, string $apiName): array
    {
        $result = [];
        foreach ($api->responseParam as $param) {
            if ($param instanceof ResParam) {
                $result[] = [
                    'name' => $param->name,
                    'exampleValue' => $param->exampleValue,
                    'type' => $param->paramType->value,
                    'description' => is_string($param->description)
                        ? $param->description : $param->description?->toString(),
                ];
            } elseif ($param instanceof Param) {
                // 兼容早期使用 Param 定义响应字段的写法。
                $result[] = [
                    'name' => $param->name,
                    'exampleValue' => $param->value,
                    'type' => $param->type?->name,
                    'description' => $param->description?->toString(),
                ];
            } else {
                throw new Annotation("responseParam must contain ResParam instances in {$api->relateClass}::{$apiName}");
            }
        }
        return $result;
    }

    private function normalizeJson(mixed $value): mixed
    {
        if ($value instanceof UnitEnum) {
            return $value->name;
        }
        if (is_array($value)) {
            foreach ($value as $key => $item) {
                $value[$key] = $this->normalizeJson($item);
            }
        }
        return $value;
    }

    private function buildMenu(array $infoMap, array $path = []): string
    {
        $html = '<ul>';
        foreach ($infoMap as $name => $info) {
            $groupPath = [...$path, (string)$name];
            $children = $this->buildMenu($info['children'], $groupPath);
            // 保留包含有效子菜单的父分组，只隐藏整棵子树都没有接口的节点。
            if (empty($info['apiList']) && $children === '<ul></ul>') {
                continue;
            }
            $pathAttribute = $this->escape(json_encode($groupPath, self::JSON_FLAGS));
            $html .= '<li class="menu-group"><button type="button" class="group-toggle" aria-expanded="false" data-path="'
                . $pathAttribute . '"><span class="menu-arrow" aria-hidden="true">▸</span> '
                . $this->escape((string)$name) . '</button><ul hidden>';
            foreach (array_keys($info['apiList']) as $apiName) {
                $html .= '<li><a href="#" data-path="' . $pathAttribute . '" data-api="'
                    . $this->escape((string)$apiName) . '">' . $this->escape((string)$apiName) . '</a></li>';
            }
            if (!empty($info['children'])) {
                // 子分组的 li 直接嵌入当前分组的列表，支持任意层级。
                $html .= substr($children, 4, -5);
            }
            $html .= '</ul></li>';
        }
        return $html . '</ul>';
    }

    private function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    // 源码分文件维护，生成时内联到模板，输出仍为独立 HTML。
    private function readTemplate(): string
    {
        $template = $this->readTemplateFile(__DIR__ . '/doc.tpl');
        return preg_replace_callback(
            '/\{\{\$asset:([a-z0-9-]+\.(?:css|js))\}\}/',
            fn(array $match): string => $this->readTemplateFile(__DIR__ . '/assets/' . $match[1]),
            $template
        );
    }

    private function readTemplateFile(string $path): string
    {
        $content = file_get_contents($path);
        if ($content === false) {
            throw new Annotation("Unable to read document template file {$path}");
        }
        return $content;
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
