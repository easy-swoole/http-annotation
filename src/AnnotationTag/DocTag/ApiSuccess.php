<?php


namespace EasySwoole\HttpAnnotation\AnnotationTag\DocTag;


use EasySwoole\Annotation\AbstractAnnotationTag;

/**
 * Class ApiSuccess
 * @package EasySwoole\HttpAnnotation\AnnotationTag\DocTag
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