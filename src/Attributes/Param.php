<?php

namespace EasySwoole\HttpAnnotation\Attributes;

use Attribute;
use EasySwoole\HttpAnnotation\Bean\Description\Text;
use EasySwoole\HttpAnnotation\Bean\ParamParseState;
use EasySwoole\HttpAnnotation\Enum\ParamFrom;
use EasySwoole\HttpAnnotation\Enum\ParamType;
use EasySwoole\HttpAnnotation\Exception\Annotation;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use Psr\Http\Message\ServerRequestInterface;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class Param
{
    // null 表示尚未解析；解析成功后保存本次结果，与公开的默认值 value 分离。
    private ?ParamParseState $parseState = null;

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
    ) {
        $this->normalizeSources();
        $this->normalizeValidators();
        $this->checkRuleConflicts();

        // 普通字符串说明统一包装为 Text，方便文档生成使用同一说明对象接口。
        if (is_string($this->description)) {
            $this->description = new Text($this->description);
        }
    }


    private function normalizeSources(): void
    {
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
    }

    private function normalizeValidators(): void
    {
        // 按静态规则名建立索引，便于查找可选规则；同名规则以后定义的为准。
        $temp = [];
        /** @var AbstractValidator $item */
        foreach ($this->validate as $index => $item) {
            if (!$item instanceof AbstractValidator) {
                $type = get_debug_type($item);
                throw new Annotation("param {$this->name} validate[{$index}] must be an AbstractValidator instance, {$type} given");
            }
            $temp[$item::ruleName()] = $item;
        }
        $this->validate = $temp;
    }

    private function checkRuleConflicts(): void
    {
        // 在参数定义阶段拒绝“可选”和“必填/非空”的冲突，避免运行时跳过校验产生歧义。
        // IgnoreValidatorWhenEmpty 同属跳过校验规则，NotEmpty 同属要求非空规则。
        $optionalRules = [];
        $requiredRules = [];
        foreach (array_keys($this->validate) as $ruleName) {
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
    }

    /**
     * 读取并缓存请求参数；仅实际取到值时按 type 转换，未命中来源时原样保留默认值。
     * 不传 request 时只读取当前值，不触发解析或改变已解析状态。
     */
    public function parsedValue(?ServerRequestInterface $request = null)
    {
        if ($this->parseState !== null) {
            return $this->parseState->parsedValue;
        }
        if ($request === null) {
            return $this->value;
        }

        // 先在局部状态中完成解析，失败时不缓存半成品，也不覆盖默认值。
        $state = new ParamParseState();

        $state->parse($this, $request);

        // 同一实例只解析一次，避免跨字段校验重复取值或重复转换。
        $this->parseState = $state;
        return $state->parsedValue;
    }

    // 用于必填判断及 ignorePassArgWhenNotSet，不等同于值是否为空。
    public function hasSet(): bool
    {
        return $this->parseState?->hasSet ?? false;
    }

    public function __clone()
    {
        // 控制器从缓存定义克隆参数时，同时克隆验证器，隔离请求间的规则实例状态。
        // 副本从默认值开始重新解析，避免已解析实例的请求状态进入下一次请求。
        // 对象类型的默认值仍为浅拷贝，此处不自动深拷贝任意业务对象。
        $this->parseState = null;
        /** @var AbstractValidator $item */
        foreach ($this->validate as $item) {
            $this->validate[$item::ruleName()] = clone $item;
        }
    }
}
