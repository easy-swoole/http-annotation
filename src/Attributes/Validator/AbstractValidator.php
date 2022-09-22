<?php

namespace EasySwoole\HttpAnnotation\Attributes\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use Psr\Http\Message\ServerRequestInterface;

abstract class AbstractValidator
{
    /**
     * @var string|null
     */
    private ?string $errorMsg;

    private array $params = [];

    private ?Param $param = null;

    /**
     * @param Param|null $param
     */
    public function setParam(?Param $param): void
    {
        $this->param = $param;
    }

    private ?ServerRequestInterface $request = null;

    private $args = null;

    function execute(Param $param,ServerRequestInterface $request):bool
    {
        $this->param = $param;
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
        return $this->param;
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
            unset($list['param']);
            unset($list['request']);
            unset($list['args']);
            $this->args = $list;
        }
        return $this->args;
    }

    function errorMsg(?string $msg = null):?string
    {
        if(!empty($msg)){
            $this->errorMsg = $msg;
            return null;
        }
        if(!empty($this->errorMsg)){
            $tpl = $this->errorMsg;
            $tpl = str_replace('{#name}',$this->param->name,$tpl);
            foreach ($this->getRuleArgs() as $key => $val){
                if(is_callable($val)){
                    $val = "Custom Func Exec Result";
                }elseif (is_array($val)){
                    $val = json_encode($val,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
                }elseif(is_object($val)){
                    if(method_exists($val,"__tostring")){
                        $val = $val->__tostring();
                    }else{
                        $val = (string)$val;
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

    function isIgnoreCheck(Param $param):bool
    {
        //当配置了option选项，且传参不是null,也就是没传的时候，允许忽略检查
        if($param->isOptional() && (!$param->hasSet()) && ($param->parsedValue() === null)){
            return true;
        }
        return false;
    }
}