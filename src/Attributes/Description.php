<?php

namespace EasySwoole\HttpAnnotation\Attributes;

#[\Attribute]
class Description implements \JsonSerializable
{
    const PLAIN_TEXT = 'PLAIN_TEXT';

    const  PLAIN_TEXT_FILE = 'PLAIN_TEXT_FILE';

    const JSON = 'JSON';
    const JSON_FILE = 'JSON_FILE';
    const XML = 'XML';

    const XML_FILE = 'XML_FILE';

    const MARKDOWN = 'MARKDOWN';

    const MARKDOWN_FILE = 'MARKDOWN_FILE';

    function __construct(public string $desc,public string $type = Description::PLAIN_TEXT)
    {

    }

    public function jsonSerialize(): mixed
    {
        $des = $this->desc;
        if(in_array($this->type,[self::PLAIN_TEXT_FILE,self::JSON_FILE,self::MARKDOWN_FILE])){

            $des = file_get_contents($this->desc);
        }

        if($this->type == self::JSON || $this->type == self::JSON_FILE){
            $des = json_decode($this->desc,true);
            $des = json_encode($des,JSON_PRETTY_PRINT);
        }

        if($this->type == self::XML || $this->type == self::XML_FILE){

        }


        return [
            "desc"=>$des,
            "type"=>$this->type
        ];
    }
}