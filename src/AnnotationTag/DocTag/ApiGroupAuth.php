<?php


namespace EasySwoole\HttpAnnotation\AnnotationTag\DocTag;


use EasySwoole\Annotation\AbstractAnnotationTag;

/**
 * Class ApiGroupAuth
 * @package EasySwoole\HttpAnnotation\AnnotationTag\DocTag
 * @Annotation
 */
class ApiGroupAuth extends AbstractAnnotationTag
{
    public function tagName(): string
    {
        return 'ApiGroupAuth';
    }

    public function assetValue(?string $raw)
    {
        // TODO: Implement assetValue() method.
    }

}