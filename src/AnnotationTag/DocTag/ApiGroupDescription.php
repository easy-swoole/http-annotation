<?php


namespace EasySwoole\HttpAnnotation\AnnotationTag\DocTag;


use EasySwoole\Annotation\AbstractAnnotationTag;

/**
 * Class ApiGroupDescription
 * @package EasySwoole\HttpAnnotation\AnnotationTag\DocTag
 * @Annotation
 */
class ApiGroupDescription extends AbstractAnnotationTag
{
    public function tagName(): string
    {
       return 'ApiGroupDescription';
    }

    public function assetValue(?string $raw)
    {
        // TODO: Implement assetValue() method.
    }

}