<?php
namespace EasySwoole\HttpAnnotation\AnnotationTag;



/**
 * Class ApiResponseParam
 * @package EasySwoole\HttpAnnotation\AnnotationTag
 * @Annotation
 */
class ApiResponseParam extends Param
{
    public function tagName(): string
    {
        return 'ApiResponseParam';
    }

}