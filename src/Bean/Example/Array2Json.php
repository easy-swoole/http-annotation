<?php

namespace EasySwoole\HttpAnnotation\Bean\Example;

class Array2Json implements ExampleInterface
{
    public array $formDaa;
    private string|null $content = null;

    public bool $isSuccessResponse;

    function __construct(array $data,bool $isSuccessResponse = true)
    {
        $this->formDaa = $data;
        $this->isSuccessResponse = $isSuccessResponse;
    }

    function isSuccessResponse(): bool
    {
        return $this->isSuccessResponse;
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