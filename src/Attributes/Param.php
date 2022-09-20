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
use Psr\Http\Message\ServerRequestInterface;

#[\Attribute(\Attribute::TARGET_ALL|\Attribute::IS_REPEATABLE)]
class Param
{
    private bool $isParsed = false;
    private bool|null $isOptional = null;
    private bool $isNullData = false;

    /**
     * @throws Annotation
     */
    public function __construct(
        public string                  $name,
        public ParamFrom|array         $from,
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
    }

    public function parsedValue(?ServerRequestInterface $request = null)
    {
        if($this->isParsed){
            return $this->value;
        }
        if($request){
            $hit = false;
            if($this->from instanceof HttpMethod){
                $fromList = [$this->from];
            }else{
                $fromList = $this->from;
            }
            foreach ($fromList as $from){
                if(!$hit){
                    switch ($from){
                        case ParamFrom::GET:{
                            $data = $request->getQueryParams();
                            if(isset($data[$this->name])){
                                $hit = true;
                                $this->value = $data[$this->name];
                                break;
                            }
                        }
                        case ParamFrom::FORM_POST:{
                            $data = $request->getParsedBody();
                            if(isset($data[$this->name])){
                                $hit = true;
                                $this->value = $data[$this->name];
                                break;
                            }
                        }
                        case ParamFrom::JSON:{
                            $data = json_decode($request->getBody()->__toString(),true);
                            if(!is_array($data)){
                                $data = [];
                            }
                            if(isset($data[$this->name])){
                                $hit = true;
                                $this->value = $data[$this->name];
                                break;
                            }
                        }
                        case ParamFrom::XML:
                        case ParamFrom::RAW_POST:{
                            $hit = true;
                            $this->value = $request->getBody()->__toString();
                            break;
                        }
                        case ParamFrom::FILE:{
                            $data = $request->getUploadedFile($this->name);
                            if(!empty($data)){
                                $hit = true;
                                $this->value = $data;
                                break;
                            }
                        }
                        case ParamFrom::DI:{
                            $data = IOC::getInstance()->get($this->name);
                            if(!empty($data)){
                                $hit = true;
                                $this->value = $data;
                                break;
                            }
                        }
                        case ParamFrom::CONTEXT:{
                            $data = ContextManager::getInstance()->get($this->name);
                            if(!empty($data)){
                                $hit = true;
                                $this->value = $data;
                                break;
                            }
                        }
                        case ParamFrom::COOKIE:{
                            $data = $request->getCookieParams($this->name);
                            if(!empty($data)){
                                $hit = true;
                                $this->value = $data;
                                break;
                            }
                        }
                        case ParamFrom::HEADER:{
                            $data = $request->getHeader($this->name);
                            $hit = true;
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
                                $hit = true;
                                $this->value = $data;
                                break;
                            }
                        }
                    }
                }else{
                    break;
                }
            }
            $this->isParsed = true;
            if($hit && ($this->value === null)){
                $this->isNullData = true;
            }
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

    public function isNullData(): bool
    {
        return $this->isNullData;
    }
}