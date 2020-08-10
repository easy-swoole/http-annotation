<?php


namespace EasySwoole\HttpAnnotation\Utility;


use EasySwoole\HttpAnnotation\Annotation\ParserInterface;

class AnnotationDoc
{
    private $scanner;

    function __construct(?ParserInterface $parser = null)
    {
        $this->scanner = new Scanner($parser);
    }

    function render($dirOrFile)
    {
        $list = $this->scanner->scanAnnotations($dirOrFile);
    }
}
