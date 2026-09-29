<?php

namespace EasySwoole\HttpAnnotation\Bean\Description;

class Markdown implements DescriptionInterface
{
    private string $markdown;

    function __construct(string $markdownFile)
    {
        if (!file_exists($markdownFile)) {
            throw new \Exception("markdown file {$markdownFile} not exists");
        }
        $this->markdown = file_get_contents($markdownFile);
    }

    function toString():string
    {
        return $this->markdown;
    }
}