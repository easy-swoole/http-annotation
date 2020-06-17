<?php


namespace EasySwoole\HttpAnnotation\AnnotationTag;

use EasySwoole\Annotation\AbstractAnnotationTag;

/**
 * Class ApiDescription
 * @package EasySwoole\HttpAnnotation\AnnotationTag
 * @Annotation
 */
class ApiDescription extends AbstractAnnotationTag
{
    /**
     * @var string text|file
     */
    public $type = 'text';

    public function tagName(): string
    {
        return 'ApiDescription';
    }
}