<?php


namespace EasySwoole\HttpAnnotation\AnnotationTag;


use EasySwoole\Annotation\AbstractAnnotationTag;

/**
 * Class ApiRequestExample
 * @package EasySwoole\HttpAnnotation\AnnotationTag
 * @Annotation
 */
class ApiRequestExample extends AbstractAnnotationTag
{

    public $content;

    public function tagName(): string
    {
        return 'ApiRequestExample';
    }

    public function assetValue(?string $raw)
    {
        $this->content = $raw;
    }
}