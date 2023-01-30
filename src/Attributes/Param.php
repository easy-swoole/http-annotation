<?php

namespace EasySwoole\HttpAnnotation\Attributes;

use EasySwoole\Component\Context\ContextManager;
use EasySwoole\Component\Di as IOC;
use EasySwoole\Http\AbstractInterface\AbstractRouter;
use EasySwoole\HttpAnnotation\Attributes\Validator\Optional;
use EasySwoole\HttpAnnotation\Enum\HttpMethod;
use EasySwoole\HttpAnnotation\Enum\ParamType;
use EasySwoole\HttpAnnotation\Enum\ParamFrom;
use EasySwoole\HttpAnnotation\Exception\Annotation;
use EasySwoole\Spl\SplArray;
use Psr\Http\Message\ServerRequestInterface;

#[\Attribute(\Attribute::TARGET_ALL|\Attribute::IS_REPEATABLE)]
class Param
{
    private bool $isParsed = false;
    private bool|null $isOptional = null;
    private bool $hasSet = false;
    private array $parentStack = [];

    /**
     * @throws Annotation
     */
    public function __construct(
        public string                  $name,
        public ParamFrom               $from = ParamFrom::GET,
        public ?array                  $validate = [],
        public                         $value = null,
        public bool                    $deprecated = false,
        public bool                    $unset = false,
        public Description|string|null $description = null,
        public ?ParamType              $type = null,
        public array                   $subObject = []
    ){
        if($this->description){
            if(!$this->description instanceof Description){
                $this->description = new Description(desc:$this->description);
            }
            if($this->description->type != Description::PLAIN_TEXT){
                throw new Annotation("description only allow PLAIN_TEXT type in Param attribute");
            }
        }
        if(!empty($this->subObject)){
            /** @var Param $item */
            $temp = $this->parentStack;
            $temp[] = $this->name;
            foreach ($this->subObject as $item){
                $item->parentStack($temp);
            }
        }
    }

    function parentStack(?array $stack = null):array
    {
        if($stack !== null){
            $this->parentStack = $stack;
        }
        return $this->parentStack;
    }

    public function parsedValue(?ServerRequestInterface $request = null)
    {
        if($this->isParsed){
            return $this->value;
        }
        if($request){
            switch ($this->from){
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
                    $data = json_decode($request->getBody()->__toString(),true);
                    if(!is_array($data)){
                        $data = [];
                    }
                    if(empty($this->parentStack)){
                        if(isset($data[$this->name])){
                            $this->hasSet = true;
                            $this->value = $data[$this->name];
                        }
                    }else{
                        foreach ($this->parentStack as $stack){
                            if(isset($data[$stack])){
                                $data = $data[$stack];
                            }
                        }
                        if(is_array($data) && isset($data[$this->name])){
                            $this->hasSet = true;
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
                    if(empty($this->parentStack)){
                        if(isset($data[$this->name])){
                            $this->hasSet = true;
                            $this->value = $data[$this->name];
                        }
                    }else{
                        foreach ($this->parentStack as $stack){
                            if(isset($data[$stack])){
                                $data = $data[$stack];
                            }
                        }
                        if(is_array($data) && isset($data[$this->name])){
                            $this->hasSet = true;
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
                    $data = $request->getCookieParams($this->name);
                    if(!empty($data)){
                        $this->hasSet = true;
                        $this->value = $data;
                    }
                    break;
                }
                case ParamFrom::HEADER:{
                    $data = $request->getHeader($this->name);
                    $this->hasSet = true;
                    if(!empty($data)){
                        $this->value = $data[0];
                    }else{
                        $this->value = null;
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
                }
            }

            $this->isParsed = true;
        }
        return $this->value;
    }

    public function isOptional(): bool
    {
        if($this->isOptional === null){
            foreach ($this->validate as $rule){
                if($rule instanceof Optional){
                    $this->isOptional = true;
                    return $this->isOptional;
                }
            }
            $this->isOptional = false;
        }
        return $this->isOptional;
    }

   public function hasSet():bool
   {
       return $this->hasSet;
   }
}