<?php

namespace EasySwoole\HttpAnnotation\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
use Psr\Http\Message\ServerRequestInterface;

class Decimal extends AbstractValidator
{
    public int|null $accuracy;

    function __construct(int|null $accuracy = null,string|null $errorMsg = null)
    {
        if($accuracy !== null && $accuracy < 0){
            $accuracy = 0;
        }
        $this->accuracy = $accuracy;
        if(empty($errorMsg)){
            $errorMsg = "{#validateParam} must be decimal";
            if($accuracy > 0){
                $errorMsg = $errorMsg ." with {$accuracy} accuracy";
            }
        }
        $this->errorMsgTpl($errorMsg);
    }

    protected function validate(ValidateRequest $validateRequest): bool
    {
        $itemData = $validateRequest->validateParam->parsedValue();
        //没有传参则降级验证为浮点数即可
        if ($this->accuracy === null) {
            if (is_float($itemData)) {
                return true;
            } else {
                return false;
            }
        }
        if ($this->accuracy === 0) {
            if (is_float($itemData)) {
                // 容错处理 如果小数点后设置0位 则验整数
                return filter_var($itemData, FILTER_VALIDATE_INT) !== false;
            } else {
                return false;
            }
        }
        return (bool)preg_match( "/^-?(([1-9]\d*)|0)\.\d{1,$this->accuracy}$/", (string)$itemData);
    }

    function ruleName(): string
    {
        return "Decimal";
    }
}