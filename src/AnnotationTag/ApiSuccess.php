<?php


namespace EasySwoole\HttpAnnotation\AnnotationTag;


use EasySwoole\Annotation\AbstractAnnotationTag;

/**
 * Class ApiSuccess
 * @package EasySwoole\HttpAnnotation\AnnotationTag
 * @Annotation
 */
class ApiSuccess extends AbstractAnnotationTag
{
    protected $content;
    public function tagName(): string
    {
        return 'ApiSuccess';
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