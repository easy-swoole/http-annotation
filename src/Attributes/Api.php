<?php

namespace EasySwoole\HttpAnnotation\Attributes;

use EasySwoole\HttpAnnotation\Bean\Description\Markdown;
use EasySwoole\HttpAnnotation\Bean\Description\Text;
use EasySwoole\HttpAnnotation\Bean\Example\ExampleInterface;
use EasySwoole\HttpAnnotation\Bean\Example\ResParam;
use EasySwoole\HttpAnnotation\Enum\ContentType;
use EasySwoole\HttpAnnotation\Enum\HttpMethod;
use EasySwoole\HttpAnnotation\Enum\ParamFrom;
use EasySwoole\HttpAnnotation\Exception\Annotation;

#[\Attribute]
class Api
{
    public string $relateClass;
    public string $relateMethod;
    public string $apiName;

    function __construct(
        public HttpMethod    $allowMethod = HttpMethod::GET,
        public bool          $registerRouter = false,
        public array         $requestParam = [],
        /**
         * @var array<ResParam>
         */
        public array         $responseParam = [],
        /**
         * @var array<ExampleInterface>
         */
        public array         $requestExamples = [],
        /**
         * @var array<ExampleInterface>
         */
        public array         $responseExamples = [],
        public string|null|Markdown|Text   $description = null,
        public bool          $deprecated = false,
        // GET、HEAD 无请求体；其他方法未指定时使用 multipart/form-data。
        public ?ContentType   $acceptContentType = null
    ){
        $withoutBody = in_array($this->allowMethod, [HttpMethod::GET, HttpMethod::HEAD], true);
        if ($withoutBody && $this->acceptContentType !== null) {
            throw new Annotation("{$this->allowMethod->name} must not define acceptContentType");
        }
        if (!$withoutBody && $this->acceptContentType === null) {
            $this->acceptContentType = ContentType::FORM_DATA;
        }
        $bodySources = [ParamFrom::POST, ParamFrom::JSON, ParamFrom::XML, ParamFrom::RAW_POST, ParamFrom::FILE];
        $acceptedSources = match ($this->acceptContentType) {
            ContentType::FORM_DATA => [ParamFrom::POST, ParamFrom::FILE],
            ContentType::FORM_URLENCODED => [ParamFrom::POST],
            ContentType::JSON => [ParamFrom::JSON],
            ContentType::XML => [ParamFrom::XML],
            ContentType::RAW => [ParamFrom::RAW_POST],
            null => [],
        };
        $temp = [];
        /** @var Param $item */
        foreach ($this->requestParam as $item){
            if (!$item instanceof Param) {
                throw new Annotation('requestParam must contain Param instances');
            }
            if(!empty($item->ignoreAction)){
                throw new Annotation('ignoreAction cannot set in controller action,only onRequest method allowed');
            }
            $sources = is_array($item->from) ? $item->from : [$item->from];
            if (empty($sources)) {
                throw new Annotation("param {$item->name} must define at least one FROM source");
            }
            foreach ($sources as $source) {
                if (!$source instanceof ParamFrom) {
                    throw new Annotation("param {$item->name} FROM must be a ParamFrom enum");
                }
                if (in_array($source, $bodySources, true) && !in_array($source, $acceptedSources, true)) {
                    $contentType = $this->acceptContentType?->name ?? 'NONE';
                    throw new Annotation("param {$item->name} FROM {$source->name} is not allowed for {$this->allowMethod->name} with acceptContentType {$contentType}");
                }
            }
            if(isset($temp[$item->name])){
                throw new Annotation("param {$item->name} define duplicate");
            }
            $temp[$item->name] = $item;
        }
        $this->requestParam = $temp;
        // 响应字段只接受 ResParam，配置错误在定义阶段发现，保留声明顺序。
        $responseNames = [];
        foreach ($this->responseParam as $index => $param) {
            if (!$param instanceof ResParam) {
                $type = get_debug_type($param);
                throw new Annotation("responseParam[{$index}] must be a ResParam instance, {$type} given");
            }
            if (trim($param->name) === '') {
                throw new Annotation("responseParam[{$index}] name must not be empty");
            }
            if (isset($responseNames[$param->name])) {
                throw new Annotation("responseParam {$param->name} define duplicate");
            }
            $responseNames[$param->name] = true;
        }
        foreach (['requestExamples', 'responseExamples'] as $property) {
            foreach ($this->{$property} as $index => $example) {
                if (!$example instanceof ExampleInterface) {
                    $type = get_debug_type($example);
                    throw new Annotation("{$property}[{$index}] must be an ExampleInterface instance, {$type} given");
                }
            }
        }
        if(is_string($this->description)){
            $this->description = new Text($this->description);
        }
    }
}
