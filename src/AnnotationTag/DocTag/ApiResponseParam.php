<?php


namespace EasySwoole\HttpAnnotation\AnnotationTag\DocTag;


use EasySwoole\HttpAnnotation\AnnotationTag\Param;

/**
 * Class ApiResponseParam
 * @package EasySwoole\HttpAnnotation\AnnotationTag\DocTag
 * @Annotation
 */
class ApiResponseParam extends Param
{
    public function tagName(): string
    {
        return 'ApiResponseParam';
    }

}