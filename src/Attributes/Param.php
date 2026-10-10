<?php

namespace EasySwoole\HttpAnnotation\Attributes;

use EasySwoole\Component\Context\ContextManager;
use EasySwoole\Component\Di as IOC;
use EasySwoole\HttpAnnotation\Bean\Description\Text;
use EasySwoole\HttpAnnotation\Enum\ParamFrom;
use EasySwoole\HttpAnnotation\Enum\ParamType;
use EasySwoole\HttpAnnotation\Exception\Annotation;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use Psr\Http\Message\ServerRequestInterface;

#[\Attribute(\Attribute::TARGET_METHOD|\Attribute::IS_REPEATABLE)]
class Param
{
    // 标记当前实例是否已完成请求取值和类型转换，后续读取直接返回缓存值。
    private bool $isParsed = false;
    // 标记是否从声明的来源实际取到值；使用默认值不会将其设为 true。
    private bool $hasSet = false;

    /**
     * @throws Annotation
     */
    public function __construct(
        public string                  $name,
        public ParamFrom|array         $from = ParamFrom::GET,
        public array                   $validate = [],
        public mixed                   $value = null,
        public bool                    $deprecated = false,
        public string|null|Text        $description = null,
        public ?ParamType              $type = ParamType::STRING,
        public array                   $ignoreAction = [],
        public bool                    $ignorePassArgWhenNotSet = false,
    ){
        // 单个来源统一转成数组，保留声明顺序，供解析时逐个尝试。
        $this->from = is_array($this->from) ? array_values($this->from) : [$this->from];
        if (!$this->from) {
            throw new Annotation("param {$this->name} must define at least one FROM source");
        }
        foreach ($this->from as $source) {
            if (!$source instanceof ParamFrom) {
                throw new Annotation("param {$this->name} FROM must be a ParamFrom enum");
            }
        }
        // 文件上传只能使用单一 FILE 来源，避免与普通字段来源混用。
        if (in_array(ParamFrom::FILE, $this->from, true) && count($this->from) !== 1) {
            $sources = implode(', ', array_map(static fn(ParamFrom $source): string => $source->name, $this->from));
            throw new Annotation("param {$this->name} FROM [{$sources}]: FILE must be the only source");
        }
        // 按静态规则名建立索引，便于查找可选规则；同名规则以后定义的为准。
        $temp = [];
        /** @var AbstractValidator $item */
        foreach ($this->validate as $item){
            $temp[$item::ruleName()] = $item;
        }
        $this->validate = $temp;
        // 在参数定义阶段拒绝“可选”和“必填/非空”的冲突，避免运行时跳过校验产生歧义。
        // IgnoreValidatorWhenEmpty 同属跳过校验规则，NotEmpty 同属要求非空规则。
        $optionalRules = [];
        $requiredRules = [];
        foreach (array_keys($temp) as $ruleName) {
            if (str_starts_with($ruleName, 'Optional') || $ruleName === 'IgnoreValidatorWhenEmpty') {
                $optionalRules[] = $ruleName;
            }
            if (str_starts_with($ruleName, 'Required') || $ruleName === 'NotEmpty') {
                $requiredRules[] = $ruleName;
            }
        }
        if ($optionalRules !== [] && $requiredRules !== []) {
            throw new Annotation("param {$this->name}: optional rules [" . implode(', ', $optionalRules)
                . '] and required rules [' . implode(', ', $requiredRules) . '] are mutually exclusive');
        }
        // 普通字符串说明统一包装为 Text，方便文档生成使用同一说明对象接口。
        if(is_string($this->description)){
            $this->description = new Text($this->description);
        }
    }


    /**
     * 读取并缓存请求参数；未命中任何来源时保留默认值，再按 type 转换。
     * 不传 request 时只读取当前值，不触发解析或改变已解析状态。
     */
    public function parsedValue(?ServerRequestInterface $request = null)
    {
        if($this->isParsed){
            return $this->value;
        }
        if(($request === null)){
            return $this->value;
        }

        // GET、POST、JSON、XML 使用 isset 判断：null 视为未设置，空字符串和 0 仍算已设置。
        foreach ($this->from as $source) {
            switch ($source){
                case ParamFrom::GET:{
                    $data = $request->getQueryParams();
                    if(isset($data[$this->name])){
                        $this->hasSet = true;
                        $this->value = $data[$this->name];
                    }
                    break;
                }
                case ParamFrom::POST:{
                    $data = $request->getParsedBody();
                    if(isset($data[$this->name])){
                        $this->hasSet = true;
                        $this->value = $data[$this->name];
                    }
                    break;
                }
                case ParamFrom::JSON:{
                    // 将 JSON 对象解码为关联数组，再按参数名取值。
                    $data = $request->getBody()->__toString();
                    if(empty($data)){
                        $data = [];
                    }else{
                        $data = json_decode($data,true) ?: [];
                    }

                    if(isset($data[$this->name])){
                        $this->hasSet = true;
                        if(!empty($this->subObject)){
                            $temps = [];
                            /** @var Param $item */
                            foreach ($this->subObject as $item){
                                $temps[$item->name] = $item->parsedValue($request,$data[$this->name]);
                            }
                            $this->value = $temps;
                        }else{
                            $this->value = $data[$this->name];
                        }
                    }

                    break;
                }
                case ParamFrom::XML:{
                    $xml = $request->getBody()->__toString();
                    // xml 转数组
                    $data = json_decode(json_encode(simplexml_load_string($xml)), true);
                    if(!is_array($data)){
                        $data = [];
                    }
                    if(isset($data[$this->name])){
                        $this->hasSet = true;
                        if(!empty($this->subObject)){
                            $temps = [];
                            /** @var Param $item */
                            foreach ($this->subObject as $item){
                                $temps[$item->name] = $item->parsedValue($request,$data[$this->name]);
                            }
                            $this->value = $temps;
                        }else{
                            $this->value = $data[$this->name];
                        }
                    }

                    break;
                }
                case ParamFrom::RAW_POST:{
                    // RAW_POST 使用整个请求体；即使内容为空，也标记为已设置。
                    $this->hasSet = true;
                    $this->value = $request->getBody()->__toString();
                    break;
                }
                case ParamFrom::FILE:{
                    // 保留上传文件对象，交由文件规则校验，不读取文件内容。
                    $data = $request->getUploadedFile($this->name);
                    if(!empty($data)){
                        $this->hasSet = true;
                        $this->value = $data;
                    }
                    break;
                }
                case ParamFrom::DI:{
                    // 从依赖注入容器读取；当前以非 empty 值作为命中条件。
                    $data = IOC::getInstance()->get($this->name);
                    if(!empty($data)){
                        $this->hasSet = true;
                        $this->value = $data;
                    }
                    break;
                }
                case ParamFrom::CONTEXT:{
                    // 从当前协程上下文读取；当前以非 empty 值作为命中条件。
                    $data = ContextManager::getInstance()->get($this->name);
                    if(!empty($data)){
                        $this->hasSet = true;
                        $this->value = $data;
                    }
                    break;
                }
                case ParamFrom::COOKIE:{
                    // 仅 null 视为未命中，允许 Cookie 值为空字符串或 0。
                    $cookies = $request->getCookieParams();
                    $data = $cookies[$this->name] ?? null;
                    if($data !== null){
                        $this->hasSet = true;
                        $this->value = $data;
                    }
                    break;
                }
                case ParamFrom::HEADER:{
                    // Swoole 请求头名称统一为小写；多值请求头只取第一项。
                    $data = $request->getHeader(strtolower($this->name));
                    if(!empty($data)){
                        $this->hasSet = true;
                        $this->value = $data[0];
                    }
                    break;
                }
            }

            // 按声明顺序读取，命中后不再检查后续来源。
            if ($this->hasSet) {
                break;
            }
        }

        // 类型转换先于验证器执行，默认值也会转换；type 为 null 时保留原值（例如数组）。
        // 转换不代表数据合法，且不会改变 hasSet，合法性仍需由验证器检查。
        if($this->type != null){
            switch ($this->type){
                case ParamType::STRING:{
                    $this->value = (string)$this->value;
                    break;
                }
                case ParamType::INT:{
                    $this->value = (int)$this->value;
                    break;
                }
                case ParamType::FLOAT:
                case ParamType::REAL:
                case ParamType::DOUBLE:{
                    $this->value = (float)$this->value;
                    break;
                }
                case ParamType::BOOLEAN:{
                    // 使用 PHP 布尔转换规则，字符串 "false" 会转换为 true。
                    $this->value = (bool)$this->value;
                    break;
                }
                case ParamType::NULL_WHILE_EMPTY:{
                    // PHP empty 值转为 null，但特意保留整数 0 和字符串 '0'。
                    if(empty($this->value) && (($this->value !== 0) && ($this->value !== '0'))){
                        $this->value = null;
                    }
                    break;
                }
                case ParamType::FILE:{
                    //无需转化
                    break;
                }
            }
        }

        // 同一实例只解析一次，避免跨字段校验重复取值或重复转换。
        $this->isParsed = true;
        return $this->value;
    }

   // 用于必填判断及 ignorePassArgWhenNotSet，不等同于值是否为空。
   public function hasSet():bool
   {
       return $this->hasSet;
   }

   function __clone()
   {
       // 控制器从缓存定义克隆参数时，同时克隆验证器，隔离请求间的规则实例状态。
       // 此处只克隆验证器，不重置取值状态，也不深拷贝 value 中的对象。
       /** @var AbstractValidator $item */
       foreach ($this->validate as $item){
           $this->validate[$item::ruleName()] = clone $item;
       }
   }
}
