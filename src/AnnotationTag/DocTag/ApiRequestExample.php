<?php


namespace EasySwoole\HttpAnnotation\AnnotationTag\DocTag;


use EasySwoole\Annotation\AbstractAnnotationTag;

/**
 * Class ApiRequestExample
 * @package EasySwoole\HttpAnnotation\AnnotationTag\DocTag
 * @Annotation
 */
class ApiRequestExample extends AbstractAnnotationTag
{

    protected $content;

    public function tagName(): string
    {
        return 'ApiRequestExample';
    }

    public function assetValue(?string $raw)
    {
        $this->content = $raw;
    }

    function getContent()
    {
        return $this->content;
    }
}