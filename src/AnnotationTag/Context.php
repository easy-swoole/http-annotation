<?php


namespace EasySwoole\HttpAnnotation\AnnotationTag;

use EasySwoole\Annotation\AbstractAnnotationTag;
use EasySwoole\HttpAnnotation\Exception\Annotation\InvalidTag;

/**
 * Class Context
 * @package EasySwoole\HttpAnnotation\AnnotationTag
 * @Annotation
 */
class Context extends AbstractAnnotationTag
{
    /**
     * @var string
     */
    public $key;

    public function tagName(): string
    {
        return 'Context';
    }

    public function assetValue(?string $raw)
    {
        parse_str($raw,$str);
        if(!empty($str['key'])){
            $this->key = trim($str['key']," \t\n\r\0\x0B\"'");
        }
        if(empty($this->key)){
            throw new InvalidTag("Di key is required");
        }
    }
}