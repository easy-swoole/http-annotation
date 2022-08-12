<?php

namespace EasySwoole\HttpAnnotation\Attributes;

use EasySwoole\HttpAnnotation\Attributes\Validator\Optional;
use EasySwoole\HttpAnnotation\Exception\Annotation;
use Psr\Http\Message\ServerRequestInterface;

#[\Attribute(\Attribute::TARGET_ALL|\Attribute::IS_REPEATABLE)]
class Param
{
    const TYPE_STRING = "TYPE_STRING";
    const TYPE_INT = "TYPE_INT";
    const TYPE_DOUBLE = "TYPE_DOUBLE";
    const TYPE_REAL = "TYPE_REAL";
    const TYPE_FLOAT = "TYPE_FLOAT";
    const TYPE_BOOL = "TYPE_BOOL";

    const TYPE_LIST = "TYPE_LIST";
    const TYPE_OBJECT = "TYPE_OBJECT";

    const GET = "GET";
    const POST = "POST";
    const COOKIE = "COOKIE";
    const HEADER = "HEADER";
    const FILE = "FILE";
    const RAW = "RAW";
    const JSON = "JSON";
    const CONTEXT = "CONTEXT";
    const DI = "DI";

    private bool $isParsed = false;
    private bool|null $isOptional = null;
    private bool $isNullData = false;

    /**
     * @throws Annotation
     */
    public function __construct(
        public string                   $name,
        public string                   $from,
        public ?array                   $validate = [],
        public                          $value = null,
        public bool                     $deprecated = false,
        public bool                     $unset = false,
        public Description|string|null $description = null,
        public ?string                  $type = null,
        public array                    $subObject = []
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
            foreach ($this->from as $from){
                if(!$hit){
                    switch ($from){
                        case Param::GET:{
                            $data = $request->getQueryParams();
                            if(isset($data[$this->name])){
                                $hit = true;
                                $this->value = $data[$this->name];
                                break;
                            }
                        }
                        case Param::POST:{
                            $data = $request->getParsedBody();
                            if(isset($data[$this->name])){
                                $hit = true;
                                $this->value = $data[$this->name];
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