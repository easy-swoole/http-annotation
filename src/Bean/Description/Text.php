<?php

namespace EasySwoole\HttpAnnotation\Bean\Description;

class Text extends AbstractDescription
{

    public string $text;

    public bool $isFile;

    private string|null $content = null;

    function __construct(string $desc,bool $isFile = false)
    {
        $this->isFile = $isFile;
        $this->text = $desc;
    }

    function toString():string
    {
        if($this->content !== null){
            return $this->content;
        }

        if($this->isFile){
            if (!file_exists($this->text)) {
                throw new \Exception("text description file {$this->text} not exists");
            }
            $this->content  = file_get_contents($this->text);
        }else{
            $this->content  = $this->text;
        }

        return $this->content ;
    }
}