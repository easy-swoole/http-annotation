<?php


namespace EasySwoole\HttpAnnotation\AnnotationTag;

/**
 * Class ApiSuccess
 * @package EasySwoole\HttpAnnotation\AnnotationTag
 * @Annotation
 */
class ApiSuccess extends ApiFail
{
    public function tagName(): string
    {
        return 'ApiSuccess';
    }
}