<?php

namespace EasySwoole\HttpAnnotation\Bean\Description;

class Text implements DescriptionInterface
{

    public string $text;

    public bool $isFile;

    private string|null $content = null;

    function __construct(string $desc,bool $isFile = false)
    {
        $this->isFile = $isFile;
        if($isFile){
            if (!file_exists($desc)) {
                throw new \Exception("text description file {$desc} not exists");
            }
        }
        $this->text = $desc;

    }

    function toString():string
    {
        if($this->content !== null){
            return $this->content;
        }

        if($this->isFile){
            $text = file_get_contents($this->text);
        }else{
            $text = $this->text;
        }
        $this->content = $text;
        return $text;
    }
}