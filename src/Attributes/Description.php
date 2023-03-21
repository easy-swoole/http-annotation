<?php

namespace EasySwoole\HttpAnnotation\Attributes;

use EasySwoole\HttpAnnotation\Document\Document;

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
        //做校验
        if($this->type ==  Description::PLAIN_TEXT){
            if(is_file($this->desc)){
                $ext = explode('.',$this->desc);
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
                $this->type = $type;
            }
        }
    }

    public function jsonSerialize(): mixed
    {
        $des = $this->desc;
        if(in_array($this->type,[self::PLAIN_TEXT_FILE,self::JSON_FILE,self::MARKDOWN_FILE,self::XML_FILE])){
            $des = file_get_contents($this->desc);
        }

        if($this->type == self::JSON || $this->type == self::JSON_FILE){
            $des = json_decode($des,true);
            $des = json_encode($des,JSON_PRETTY_PRINT);
        }


        if($this->type == self::XML || $this->type == self::XML_FILE){
            $dom = new \DOMDocument('1.0');
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = true;
            if($dom->loadXML($des)){
                $des = $dom->saveXML();
            }
        }


        return [
            "desc"=>$des,
            "type"=>$this->type
        ];
    }
}