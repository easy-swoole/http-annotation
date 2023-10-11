<?php

namespace EasySwoole\HttpAnnotation\Attributes;

use EasySwoole\Component\Context\ContextManager;
use EasySwoole\Component\Di as IOC;
use EasySwoole\Http\AbstractInterface\AbstractRouter;
use EasySwoole\HttpAnnotation\Enum\ParamFrom;
use EasySwoole\HttpAnnotation\Enum\ParamType;
use EasySwoole\HttpAnnotation\Exception\Annotation;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use EasySwoole\HttpAnnotation\Validator\Optional;
use Psr\Http\Message\ServerRequestInterface;

#[\Attribute(\Attribute::TARGET_ALL|\Attribute::IS_REPEATABLE)]
class Param implements \JsonSerializable
{
    private bool $isParsed = false;
    private bool $hasSet = false;
    private array $parentStack = [];

    /**
     * @throws Annotation
     */
    public function __construct(
        public string                  $name,
        public ParamFrom|array         $from = [ParamFrom::GET,ParamFrom::POST],
        public ?array                  $validate = [],
        public                         $value = null,
        public bool                    $deprecated = false,
        public Description|string|null $description = null,
        public ?ParamType              $type = null,
        public array                   $subObject = [],
        public array                   $ignoreAction = []
    ){
        if($this->description){
            if(!$this->description instanceof Description){
                $this->description = new Description(desc:$this->description);
            }
            if($this->description->type != Description::PLAIN_TEXT){
                throw new Annotation("description only allow PLAIN_TEXT type in Param attribute");
            }
        }
        //处理validate as key => val
        $temp = [];
        /** @var AbstractValidator $item */
        foreach ($this->validate as $item){
            $temp[$item->ruleName()] = $item;
        }
        $this->validate = $temp;

        if(!empty($this->subObject)){
            //记录key层级，类似   ['result','userInfo','userName']
            $temp = $this->parentStack;
            $temp[] = $this->name;
            $list = [];
            /** @var Param $item */
            foreach ($this->subObject as $item){
                $item->parentStack($temp);
                $list[$item->name] = $item;
            }
            $this->subObject = $list;
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
            if(!empty($this->subObject)){
                $temp = [];
                foreach ($this->subObject as $key => $item){
                    $temp[$key] = $item->parsedValue($request);
                }
                $this->value = $temp;
            }else{
                if(is_array($this->from)){
                    $fromList = $this->from;
                }else{
                    $fromList = [$this->from];
                }
                foreach ($fromList as $from){
                    switch ($from){
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
                    if($this->hasSet){
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
            }


            $this->isParsed = true;
        }
        return $this->value;
    }

   public function hasSet():bool
   {
       return $this->hasSet;
   }

   function __clone()
   {
       //规则在调用层做克隆，不然循环引用。
//       $temp = [];
//       /** @var AbstractValidator $item */
//       foreach ($this->validate as $item){
//           $temp[$item->ruleName()] = clone $item;
//       }
//       $this->validate = $temp;

       $temp = [];
       /** @var Param $item */
       foreach ($this->subObject as $item){
           $temp[$item->name] = $item;
       }
       $this->subObject = $temp;
   }

    public function jsonSerialize(): mixed
    {
        $from = [];
        if(is_array($this->from)){
            foreach ($this->from as $item){
                $from[] = $item->name;
            }
        }else{
            $from[] = $this->from->name;
        }

        $validate = [];
        /** @var AbstractValidator $item */
        foreach ($this->validate as $item){
            $item->setCurrentParam($this);
            $validate[$item->ruleName()] = $item->errorMsg(null);
        }

        $type = null;
        if($this->type){
            $type = $this->type->name;
        }

        $desc = $this->description;
        if(is_string($desc)){
            $desc = new Description($desc,Description::PLAIN_TEXT);
        }

        return [
            'name'=>$this->name,
            'from'=>$from,
            'validate'=>$validate,
            'value'=>$this->value,
            'deprecated'=>$this->deprecated,
            'type'=>$type,
            'subObject'=>$this->subObject,
            'description'=>$this->description,
            'parentStack'=>$this->parentStack
        ];
    }
}