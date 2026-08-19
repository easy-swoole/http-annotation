<?php

namespace EasySwoole\HttpAnnotation\Validator\AbstractInterface;

use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
use EasySwoole\HttpAnnotation\Validator\OptionalIfParamSet;
use EasySwoole\HttpAnnotation\Validator\OptionalIfParamValInArray;
use EasySwoole\HttpAnnotation\Validator\OptionalIfParamValNoInArray;

abstract class AbstractValidator
{
    /**
     * @var string|null
     */
    private string|null $errorMsgTpl;

    private array|null $args = null;


    function execute(ValidateRequest $request):bool
    {
        if($this->isIgnoreCheck($request)){
            return true;
        }
        return $this->validate($request);
    }


    abstract protected function validate(ValidateRequest $validateRequest):bool;


    abstract function ruleName():string;


    /**
     * 规则参数请用protected
     */
    function getRuleArgs():array
    {
        if($this->args === null){
            $list = [];
            foreach ($this as $key => $val){
                $list[$key] = $val;
            }
            unset($list['errorMsgTpl']);
            unset($list['args']);
            $this->args = $list;
        }
        return $this->args;
    }

    function errorMsgTpl(string|null $msg):string
    {
        if(!empty($msg)){
            $this->errorMsgTpl = $msg;
        }
        return  $this->errorMsgTpl;
    }

    function errorMsg(ValidateRequest $validateRequest):string
    {
        $tpl = $this->errorMsgTpl;
        $tpl = str_replace('{#validateParam}',$validateRequest->validateParam->name,$tpl);
        foreach ($this->getRuleArgs() as $key => $val){
            if(is_callable($val)){
                $val = "Custom Func";
            }elseif (is_array($val)){
                $val = json_encode($val,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
            }elseif(is_object($val)){
                if(method_exists($val,"__toString")){
                    $val = $val->__toString();
                }else{
                    $val = json_encode($val,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
                }
            }else{
                $val = (string)$val;
            }
            $tpl = str_replace("{#$key}",$val,$tpl);
        }
        return $tpl;
    }

    protected function isIgnoreCheck(ValidateRequest $validateRequest):bool
    {
        $param = $validateRequest->validateParam;
        $rules = $param->validate;
        if(isset($rules['Optional'])){
            $isOptional = true;
        }else{
            $isOptional = false;
        }
        //当配置了option选项，且传参不是null,也就是没传的时候，允许忽略检查
        if($isOptional && (!$param->hasSet()) && ($param->parsedValue() === null)){
            return true;
        }

        if(isset($rules['IgnoreValidatorWhenEmpty'])){
            if((!$param->hasSet()) || empty($param->parsedValue())){
                return true;
            }
        }

        //如果自己已经传值。则返回fasle
        if($param->hasSet()){
            return false;
        }

        if(isset($rules['OptionalIfParamMiss'])){
            /** @var OptionalIfParamSet $if */
            $if = $rules['OptionalIfParamMiss'];
            $paramName = $if->getRuleArgs()['paramName'];
            $all = $validateRequest->allDefineParams;
            if(isset($all[$paramName])){
                /** @var Param $param */
                $comParam = $all[$paramName];
                return !$comParam->hasSet();
            }
            return true;
        }
        if(isset($rules['OptionalIfParamSet'])){
            /** @var OptionalIfParamSet $if */
            $if = $rules['OptionalIfParamSet'];
            $paramName = $if->getRuleArgs()['paramName'];
            $all = $validateRequest->allDefineParams;
            if(isset($all[$paramName])){
                /** @var Param $param */
                $comParam = $all[$paramName];
                return $comParam->hasSet();
            }
            return false;
        }

        if(isset($rules['OptionalIfParamValInArray'])){
            /** @var OptionalIfParamValInArray $if */
            $if = $rules['OptionalIfParamValInArray'];
            $targetParamName = $if->getRuleArgs()['paramName'];
            $inVal = $if->getRuleArgs()['inVal'];
            $all = $validateRequest->allDefineParams;
            if(isset($all[$targetParamName])){
                /** @var Param $param */
                $comParam = $all[$targetParamName];
                if($comParam->hasSet()){
                    $com = $comParam->parsedValue();
                    return in_array($com,$inVal);
                }
            }
            return false;
        }

        if(isset($rules['OptionalIfParamValNoInArray'])){
            /** @var OptionalIfParamValNoInArray $if */
            $if = $rules['OptionalIfParamValNoInArray'];
            $targetParamName = $if->getRuleArgs()['paramName'];
            $inVal = $if->getRuleArgs()['inVal'];
            $all = $validateRequest->allDefineParams;
            if(isset($all[$targetParamName])){
                /** @var Param $param */
                $comParam = $all[$targetParamName];
                if($comParam->hasSet()){
                    $com = $comParam->parsedValue();
                    return !in_array($com,$inVal);
                }
            }
            return true;
        }

        return false;
    }

    function __destruct()
    {
        var_dump('D V');
    }
}