<?php

namespace EasySwoole\HttpAnnotation\Bean\Example;

class Raw implements ExampleInterface
{

    public string $raw;

    public bool $isFile;

    private string|null $content = null;

    function __construct(string $raw, bool $isFile = false)
    {
        $this->isFile = $isFile;
        if($isFile){
            if (!file_exists($raw)) {
                throw new \Exception("raw example file {$raw} not exists");
            }
        }
        $this->raw = $raw;

    }

    function toString():string
    {
        if($this->content !== null){
            return $this->content;
        }

        if($this->isFile){
            $text = file_get_contents($this->raw);
        }else{
            $text = $this->raw;
        }
        $this->content = $text;
        return $text;
    }
}