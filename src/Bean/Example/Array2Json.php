<?php

namespace EasySwoole\HttpAnnotation\Bean\Example;

class Array2Json implements ExampleInterface
{
    public array $formDaa;
    private string|null $content = null;

    function __construct(array $data)
    {
        $this->formDaa = $data;
    }

    function toString():string
    {
        if($this->content !== null){
            return $this->content;
        }
        $this->content = json_encode($this->formDaa, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        return $this->content;
    }
}