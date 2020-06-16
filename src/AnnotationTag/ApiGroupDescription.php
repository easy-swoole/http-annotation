<?php


namespace EasySwoole\HttpAnnotation\AnnotationTag;

/**
 * Class ApiGroupDescription
 * @package EasySwoole\HttpAnnotation\AnnotationTag
 * @Annotation
 */
class ApiGroupDescription extends ApiFail
{
    public function tagName(): string
    {
       return 'ApiGroupDescription';
    }
}