<?php


namespace EasySwoole\HttpAnnotation\Utility;


use EasySwoole\HttpAnnotation\Annotation\MethodAnnotation;
use EasySwoole\HttpAnnotation\Annotation\ObjectAnnotation;
use EasySwoole\HttpAnnotation\Annotation\ParserInterface;

class AnnotationDoc
{
    private $scanner;

    function __construct(?ParserInterface $parser = null)
    {
        $this->scanner = new Scanner($parser);
    }

    function render($dirOrFile):string
    {
        $groupList = [];
        $result = '';
        $list = $this->scanner->scanAnnotations($dirOrFile);
        /** @var ObjectAnnotation $objectAnnotation */
        foreach ($list as $objectAnnotation)
        {
            if($objectAnnotation->getApiGroupTag()){
                $group = $objectAnnotation->getApiGroupTag()->groupName;
            }else{
                $group = 'default';
            }
            $groupList[$group] = [];
            if(!isset($groupList[$groupList])){

            }
        }
    }
}
