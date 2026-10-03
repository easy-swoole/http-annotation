<?php

namespace EasySwoole\HttpAnnotation\Bean\Description;

class Markdown implements DescriptionInterface
{

    public string $markdownFile;
    private string|null $content = null;

    function __construct(string $markdownFile)
    {

        $this->markdownFile = $markdownFile;
    }

    function toString():string
    {
        if($this->content !== null){
            return $this->content;
        }
        if (!file_exists($this->markdownFile)) {
            throw new \Exception("markdown description file {$this->markdownFile} not exists");
        }
        $this->content = file_get_contents($this->markdownFile);
        return $this->content;
    }
}