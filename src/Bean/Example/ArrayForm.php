<?php

namespace EasySwoole\HttpAnnotation\Bean\Example;

class ArrayForm implements ExampleInterface
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
        $this->content = http_build_query($this->formDaa);
        return $this->content;
    }
}