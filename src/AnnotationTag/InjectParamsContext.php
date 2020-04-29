<?php


namespace EasySwoole\HttpAnnotation\AnnotationTag;

use EasySwoole\Annotation\AbstractAnnotationTag;

/**
 * Class InjectParamsContext
 * @package EasySwoole\HttpAnnotation\AnnotationTag
 * @Annotation
 */
class InjectParamsContext extends AbstractAnnotationTag
{
    public $key = 'INJECT_PARAMS';

    public function tagName(): string
    {
        return 'InjectParamsContext';
    }

    public function assetValue(?string $raw)
    {
        parse_str($raw,$str);
        if(!empty($str['key'])){
            $this->key = trim($str['key']," \t\n\r\0\x0B\"'");
        }
    }
}