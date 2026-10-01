<?php

namespace EasySwoole\HttpAnnotation\Bean\Description;

class Markdown implements DescriptionInterface
{

    public string $markdownFile;
    private string|null $content = null;

    function __construct(string $markdownFile)
    {
        if (!file_exists($markdownFile)) {
            throw new \Exception("markdown description file {$markdownFile} not exists");
        }
        $this->markdownFile = $markdownFile;

    }

    function toString():string
    {
        if($this->content !== null){
            return $this->content;
        }

        $this->content = file_get_contents($this->markdownFile);
        return $this->content;
    }
}