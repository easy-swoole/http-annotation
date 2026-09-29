<?php

namespace EasySwoole\HttpAnnotation\Bean\Description;

class Text implements DescriptionInterface
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