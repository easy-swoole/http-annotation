<?php


namespace EasySwoole\HttpAnnotation\AnnotationTag;


use EasySwoole\Annotation\AbstractAnnotationTag;
use EasySwoole\Annotation\ValueParser;

/**
 * Class Method
 * @package EasySwoole\HttpAnnotation\AnnotationTag
 * @Annotation
 */
class Method extends AbstractAnnotationTag
{
    /**
     * @var array
     */
    public $allow = [];

    public function tagName(): string
    {
        return 'Method';
    }

    public function assetValue(?string $raw)
    {
        $array = ValueParser::parser($raw);
        foreach ($array as $key => $value){
            $this->$key = $value;
        }
    }
}