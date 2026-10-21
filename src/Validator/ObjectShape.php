<?php

namespace EasySwoole\HttpAnnotation\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Exception\Annotation;
use EasySwoole\HttpAnnotation\Utility;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
use EasySwoole\HttpAnnotation\Validator\MsgMap\DefaultMap;

class ObjectShape extends AbstractValidator
{
    // 子定义只读使用，每次执行克隆；不放入 getRuleArgs 的消息元数据中。
    private array $params = [];

    /** @param list<Param> $params */
    public function __construct(array $params, ?string $errorMsg = null)
    {
        foreach ($params as $index => $param) {
            if (!$param instanceof Param) {
                throw new Annotation("ObjectShape params[{$index}] must be a Param instance");
            }
            if (trim($param->name) === '' || isset($this->params[$param->name])) {
                throw new Annotation("ObjectShape param {$param->name} name must be non-empty and unique");
            }
            if ($param->ignoreAction !== []) {
                throw new Annotation("ObjectShape param {$param->name} must not define ignoreAction");
            }
            $this->params[$param->name] = $param;
        }
        if ($errorMsg !== null) {
            $this->errorMsgTpl($errorMsg);
        }
    }

    /** @return array<string, Param> */
    public function getParams(): array
    {
        return $this->params;
    }

    protected function validate(ValidateRequest $validateRequest): bool
    {
        $data = $validateRequest->validateParam->parsedValue();
        // JSON 使用关联数组解码；空对象与空列表均为 []，在这里按空对象处理。
        if (!is_array($data) || ($data !== [] && array_is_list($data))) {
            return false;
        }
        if ($validateRequest->depth >= 32) {
            throw new Annotation('ObjectShape/Each nesting must not exceed 32 levels');
        }

        $params = [];
        foreach ($this->params as $name => $definition) {
            $param = clone $definition;
            $param->parseValue($data[$name] ?? null, isset($data[$name]));
            $params[$name] = $param;
        }

        // 先解析全部兄弟字段，再校验，避免依赖定义顺序；未知字段原样保留。
        $path = $validateRequest->parameterPath ?? $validateRequest->validateParam->name;
        foreach ($params as $name => $param) {
            $context = new ValidateRequest($param, $validateRequest->request, $params,
                $validateRequest->callClass, $validateRequest->callMethod);
            $context->parameterPath = $path . '.' . $name;
            $context->depth = $validateRequest->depth + 1;
            Utility::validateParam($context, $validateRequest->validateMsgMapClass ?? DefaultMap::class);
            if ($param->ignorePassArgWhenNotSet && !$param->hasSet()) {
                unset($data[$name]);
            } else {
                $data[$name] = $param->parsedValue();
            }
        }
        $validateRequest->validateParam->setParsedValue($data);
        return true;
    }

    public static function ruleName(): string
    {
        return 'ObjectShape';
    }
}
