<?php

namespace EasySwoole\HttpAnnotation\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use Psr\Http\Message\ServerRequestInterface;

class Decimal extends AbstractValidator
{
    public ?int $accuracy;

    function __construct(?int $accuracy = null,?string $errorMsg = null)
    {
        if($accuracy !== null && $accuracy < 0){
            $accuracy = 0;
        }
        $this->accuracy = $accuracy;
        if(empty($errorMsg)){
            $errorMsg = "{#name} must be decimal";
            if($accuracy > 0){
                $errorMsg = $errorMsg ." with {$accuracy} accuracy";
            }
        }
        $this->errorMsg($errorMsg);
    }

    protected function validate(Param $param, ServerRequestInterface $request): bool
    {
        $itemData = $param->parsedValue();
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