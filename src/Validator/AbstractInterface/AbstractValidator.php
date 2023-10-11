<?php

namespace EasySwoole\HttpAnnotation\Validator\AbstractInterface;

use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\OptionalIfParamSet;
use EasySwoole\HttpAnnotation\Validator\OptionalIfParamValInArray;
use EasySwoole\HttpAnnotation\Validator\OptionalIfParamValNoInArray;
use Psr\Http\Message\ServerRequestInterface;

abstract class AbstractValidator
{
    /**
     * @var string|null
     */
    private ?string $errorMsg;

    private array $params = [];

    private ?Param $currentParam = null;

    /**
     * @param Param|null $currentParam
     */
    public function setCurrentParam(?Param $currentParam): void
    {
        $this->currentParam = $currentParam;
    }

    private ?ServerRequestInterface $request = null;

    private $args = null;

    function execute(Param $param,ServerRequestInterface $request):bool
    {
        $this->currentParam = $param;
        $this->request = $request;
        if($this->isIgnoreCheck($param)){
            return true;
        }
        try {
            return $this->validate($param,$request);
        } finally {
            //清除循环引用
            $this->request = null;
        }

    }

    /**
     * @return Param|null
     */
    public function currentCheckParam(): ?Param
    {
        return $this->currentParam;
    }

    /**
     * @return ServerRequestInterface|null
     */
    public function getRequest(): ?ServerRequestInterface
    {
        return $this->request;
    }

    abstract protected function validate(Param $param,ServerRequestInterface $request):bool;


    abstract function ruleName():string;

    function allCheckParams(?array $params = null):array
    {
        if($params === null){
            return $this->params;
        }
        $this->params = $params;
        return $this->params;
    }

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
            unset($list['errorMsg']);
            unset($list['params']);
            unset($list['currentParam']);
            unset($list['request']);
            unset($list['args']);
            $this->args = $list;
        }
        return $this->args;
    }

    function errorMsg(?string $msg = null,?bool $returnRaw = false):?string
    {
        if(!empty($msg)){
            $this->errorMsg = $msg;
            return null;
        }
        if($returnRaw){
            return $this->errorMsg;
        }
        if(!empty($this->errorMsg)){
            $tpl = $this->errorMsg;
            $tpl = str_replace('{#name}',$this->currentParam->name,$tpl);
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
        return $this->errorMsg;
    }

    protected function isIgnoreCheck(Param $param):bool
    {
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
        //如果自己已经传值。则返回fasle

        if($param->hasSet()){
            return false;
        }

        if(isset($rules['OptionalIfParamMiss'])){
            /** @var OptionalIfParamSet $if */
            $if = $rules['OptionalIfParamMiss'];
            $paramName = $if->getRuleArgs()['paramName'];
            $all = $this->allCheckParams();
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
            $all = $this->allCheckParams();
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
            $all = $this->allCheckParams();
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
            $all = $this->allCheckParams();
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
}