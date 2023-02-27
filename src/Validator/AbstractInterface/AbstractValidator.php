<?php

namespace EasySwoole\HttpAnnotation\Validator\AbstractInterface;

use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\OptionalIfParamSet;
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
        return $this->validate($param,$request);
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

        if(isset($rules['OptionalIfParamMiss'])){
            /** @var OptionalIfParamSet $if */
            $if = $rules['OptionalIfParamMiss'];
            $targetParamName = $if->getRuleArgs()['paramName'];
            if(!is_array($targetParamName)){
                $targetParamName = [$targetParamName];
            }
            $all = $this->allCheckParams();
            foreach ($targetParamName as $paramName){
                if(isset($all[$paramName])){
                    /** @var Param $param */
                    $param = $all[$paramName];
                    return !$param->hasSet();
                }
            }
        }

        if(isset($rules['OptionalIfParamSet'])){
            /** @var OptionalIfParamSet $if */
            $if = $rules['OptionalIfParamSet'];
            $targetParamName = $if->getRuleArgs()['paramName'];
            if(!is_array($targetParamName)){
                $targetParamName = [$targetParamName];
            }
            $all = $this->allCheckParams();
            foreach ($targetParamName as $paramName){
                if(isset($all[$paramName])){
                    /** @var Param $param */
                    $param = $all[$paramName];
                    return $param->hasSet();
                }
            }
        }
        return false;
    }
}