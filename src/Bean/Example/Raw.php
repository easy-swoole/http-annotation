<?php

namespace EasySwoole\HttpAnnotation\Bean\Example;

class Raw implements ExampleInterface
{
    private string $text;
    function __construct(string $desc)
    {
        $this->text = $desc;
    }

    function toString():string
    {
        return $this->text;
    }
}