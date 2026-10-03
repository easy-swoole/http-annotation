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

        $this->raw = $raw;

    }

    function toString():string
    {
        if($this->content !== null){
            return $this->content;
        }

        if($this->isFile){
            if (!file_exists($this->raw)) {
                throw new \Exception("raw example file {$this->raw} not exists");
            }
            $this->content = file_get_contents($this->raw);
        }else{
            $this->content = $this->raw;
        }
        return  $this->content;
    }
}