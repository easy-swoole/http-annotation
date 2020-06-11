<?php


namespace EasySwoole\HttpAnnotation\AnnotationTag\DocTag;


use EasySwoole\Annotation\AbstractAnnotationTag;

/**
 * Class ApiGroup
 * @package EasySwoole\HttpAnnotation\AnnotationTag\DocTag
 * @Annotation
 */
class ApiGroup extends AbstractAnnotationTag
{
    public function tagName(): string
    {
        return 'ApiGroup';
    }

    public function assetValue(?string $raw)
    {
        // TODO: Implement assetValue() method.
    }

}