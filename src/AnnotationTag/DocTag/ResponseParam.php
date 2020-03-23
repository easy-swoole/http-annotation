<?php


namespace EasySwoole\HttpAnnotation\AnnotationTag\DocTag;


use EasySwoole\HttpAnnotation\AnnotationTag\Param;

/**
 * Class ResponseParam
 * @package EasySwoole\HttpAnnotation\AnnotationTag\DocTag
 * @Annotation
 */
class ResponseParam extends Param
{
    public function tagName(): string
    {
        return 'ResponseParam';
    }

}