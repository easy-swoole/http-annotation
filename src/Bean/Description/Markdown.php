<?php

namespace EasySwoole\HttpAnnotation\Bean\Description;

class Markdown extends AbstractDescription
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
            if(empty($this->relateClass)){
                throw new \Exception("markdown description file {$this->markdownFile} not exists");
            }else{
                if(empty($this->relateMethod)){
                    throw new \Exception("markdown description file {$this->markdownFile} not exists define in class {$this->relateClass}");
                }else{
                    throw new \Exception("markdown description file {$this->markdownFile} not exists define in class {$this->relateClass} method {$this->relateMethod}");
                }
            }


        }
        $this->content = file_get_contents($this->markdownFile);
        return $this->content;
    }
}