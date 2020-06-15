<?php


namespace EasySwoole\HttpAnnotation\AnnotationTag;


use EasySwoole\Annotation\AbstractAnnotationTag;
use EasySwoole\Annotation\ValueParser;

/**
 * Class ApiGroupDescription
 * @package EasySwoole\HttpAnnotation\AnnotationTag
 * @Annotation
 */
class ApiGroupDescription extends AbstractAnnotationTag
{
    /**
     * @var string
     */
    public $desc;

    /**
     * @var string text|file
     */
    public $type = 'text';

    public function tagName(): string
    {
       return 'ApiGroupDescription';
    }

    public function assetValue(?string $raw)
    {
        $array = ValueParser::parser($raw);
        foreach ($array as $key => $value){
            $this->$key = $value;
        }
    }

}