<?php

namespace EasySwoole\HttpAnnotation\Attributes;

use EasySwoole\Component\Context\ContextManager;
use EasySwoole\Component\Di as IOC;
use EasySwoole\Http\AbstractInterface\AbstractRouter;
use EasySwoole\HttpAnnotation\Bean\Description\Text;
use EasySwoole\HttpAnnotation\Enum\ParamFrom;
use EasySwoole\HttpAnnotation\Enum\ParamType;
use EasySwoole\HttpAnnotation\Exception\Annotation;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use Psr\Http\Message\ServerRequestInterface;

#[\Attribute(\Attribute::TARGET_METHOD|\Attribute::IS_REPEATABLE)]
class Param
{
    private bool $isParsed = false;
    private bool $hasSet = false;

    /**
     * @throws Annotation
     */
    public function __construct(
        public string                  $name,
        public ParamFrom|array         $from = [ParamFrom::GET],
        public array|null              $validate = [],
        public mixed                   $value = null,
        public bool                    $deprecated = false,
        public string|null|Text        $description = null,
        public ?ParamType              $type = ParamType::STRING,
        public array                   $ignoreAction = [],
        public bool                    $ignorePassArgWhenNotSet = false,
    ){
        $this->from = is_array($this->from) ? array_values($this->from) : [$this->from];
        if (!$this->from) {
            throw new Annotation("param {$this->name} must define at least one FROM source");
        }
        foreach ($this->from as $source) {
            if (!$source instanceof ParamFrom) {
                throw new Annotation("param {$this->name} FROM must be a ParamFrom enum");
            }
        }
        if (in_array(ParamFrom::FILE, $this->from, true) && count($this->from) !== 1) {
            $sources = implode(', ', array_map(static fn(ParamFrom $source): string => $source->name, $this->from));
            throw new Annotation("param {$this->name} FROM [{$sources}]: FILE must be the only source");
        }
        //处理validate as key => val
        $temp = [];
        /** @var AbstractValidator $item */
        foreach ($this->validate as $item){
            $temp[$item->ruleName()] = $item;
        }
        $this->validate = $temp;
        if(is_string($this->description)){
            $this->description = new Text($this->description);
        }
    }


    public function parsedValue(?ServerRequestInterface $request = null)
    {
        if($this->isParsed){
            return $this->value;
        }
        if(($request === null)){
            return $this->value;
        }

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
                    $this->hasSet = true;
                    $this->value = $request->getBody()->__toString();
                    break;
                }
                case ParamFrom::FILE:{
                    $data = $request->getUploadedFile($this->name);
                    if(!empty($data)){
                        $this->hasSet = true;
                        $this->value = $data;
                    }
                    break;
                }
                case ParamFrom::DI:{
                    $data = IOC::getInstance()->get($this->name);
                    if(!empty($data)){
                        $this->hasSet = true;
                        $this->value = $data;
                    }
                    break;
                }
                case ParamFrom::CONTEXT:{
                    $data = ContextManager::getInstance()->get($this->name);
                    if(!empty($data)){
                        $this->hasSet = true;
                        $this->value = $data;
                    }
                    break;
                }
                case ParamFrom::COOKIE:{
                    $cookies = $request->getCookieParams();
                    $data = $cookies[$this->name] ?? null;
                    if($data !== null){
                        $this->hasSet = true;
                        $this->value = $data;
                    }
                    break;
                }
                case ParamFrom::HEADER:{
                    //swoole header的key，全部都是小写
                    $data = $request->getHeader(strtolower($this->name));
                    if(!empty($data)){
                        $this->hasSet = true;
                        $this->value = $data[0];
                    }
                    break;
                }
                case ParamFrom::ROUTER_PARAMS:{
                    $data = ContextManager::getInstance()->get(AbstractRouter::PARSE_PARAMS_CONTEXT_KEY);
                    if(isset($data[$this->name])){
                        $this->hasSet = true;
                        $this->value = $data;
                    }
                    break;
                }
            }

            // 按声明顺序读取，命中后不再检查后续来源。
            if ($this->hasSet) {
                break;
            }
        }

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
                    $this->value = (bool)$this->value;
                    break;
                }
                case ParamType::NULL_WHILE_EMPTY:{
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

        $this->isParsed = true;
        return $this->value;
    }

   public function hasSet():bool
   {
       return $this->hasSet;
   }

   function __clone()
   {

       /** @var AbstractValidator $item */
       foreach ($this->validate as $item){
           $this->validate[$item->ruleName()] = clone $item;
       }
   }
}
