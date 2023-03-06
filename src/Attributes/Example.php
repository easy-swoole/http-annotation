<?php

namespace EasySwoole\HttpAnnotation\Attributes;


use EasySwoole\HttpAnnotation\Exception\Annotation;

class Example implements \JsonSerializable
{
    const TYPE_PARAM_ARRAY = 'PARAM_ARRAY';
    const TYPE_DESCRIPTION = 'DESCRIPTION';
    private string $exampleType = self::TYPE_PARAM_ARRAY;

    function __construct(
        public array|Description|string $example
    ){
        if(!is_array($this->example)){
            if(is_string($this->example)){
                if(is_file($this->example)){
                    $ext = explode('.',$this->example);
                    $ext = array_pop($ext);
                    $type = Description::PLAIN_TEXT;
                    switch ($ext){
                        case 'json':{
                            $type = Description::JSON_FILE;
                            break;
                        }
                        case 'md':{
                            $type = Description::MARKDOWN_FILE;
                            break;
                        }

                        case "xml":{
                            $type = Description::XML_FILE;
                            break;
                        }
                    }
                    $this->example = new Description($this->example,$type);
                }else{
                    $this->example = new Description($this->example);
                }
            }
            $this->exampleType = self::TYPE_DESCRIPTION;
        }
    }

    public function jsonSerialize(): mixed
    {
        return [
            'example'=>$this->example,
            'exampleType'=>$this->exampleType
        ];
    }
}