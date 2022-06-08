<?php

namespace EasySwoole\HttpAnnotation\Attributes;

use EasySwoole\HttpAnnotation\Attributes\Validator\Optional;
use Psr\Http\Message\ServerRequestInterface;

#[\Attribute(\Attribute::TARGET_ALL|\Attribute::IS_REPEATABLE)]
class Param
{
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

    public function __construct(
        public string $name,
        public array $from = ["GET","POST"],
        public ?array $validate = [],
        public ?Description $description = null,
        public $value = null
    ){}

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