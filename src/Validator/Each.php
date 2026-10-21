<?php

namespace EasySwoole\HttpAnnotation\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Exception\Annotation;
use EasySwoole\HttpAnnotation\Utility;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
use EasySwoole\HttpAnnotation\Validator\MsgMap\DefaultMap;

class Each extends AbstractValidator
{
    private Param $item;

    /** @param list<AbstractValidator> $validate */
    public function __construct(array $validate, ?string $errorMsg = null)
    {
        // 复用 Param 对验证器类型、同名规则及互斥规则的定义检查。
        $this->item = new Param('[]', type: null, validate: $validate);
        if ($errorMsg !== null) {
            $this->errorMsgTpl($errorMsg);
        }
    }

    /** @return array<string, AbstractValidator> */
    public function getValidators(): array
    {
        return $this->item->validate;
    }

    protected function validate(ValidateRequest $validateRequest): bool
    {
        $data = $validateRequest->validateParam->parsedValue();
        if (!is_array($data) || !array_is_list($data)) {
            return false;
        }
        if ($validateRequest->depth >= 32) {
            throw new Annotation('ObjectShape/Each nesting must not exceed 32 levels');
        }

        $path = $validateRequest->parameterPath ?? $validateRequest->validateParam->name;
        foreach ($data as $index => $value) {
            // 每个元素独立克隆规则，避免 Func 等有状态规则在元素之间共享状态。
            $param = clone $this->item;
            $param->parseValue($value);
            $context = new ValidateRequest($param, $validateRequest->request, [],
                $validateRequest->callClass, $validateRequest->callMethod);
            $context->parameterPath = $path . '[' . $index . ']';
            $context->depth = $validateRequest->depth + 1;
            Utility::validateParam($context, $validateRequest->validateMsgMapClass ?? DefaultMap::class);
            $data[$index] = $param->parsedValue();
        }
        $validateRequest->validateParam->setParsedValue($data);
        return true;
    }

    public static function ruleName(): string
    {
        return 'Each';
    }
}
