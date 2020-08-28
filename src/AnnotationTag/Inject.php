<?php


namespace EasySwoole\HttpAnnotation\AnnotationTag;

use EasySwoole\Annotation\AbstractAnnotationTag;

/**
 * Class Inject
 * @package EasySwoole\HttpAnnotation\AnnotationTag
 * @Annotation
 */
class Inject extends AbstractAnnotationTag
{
    public $className;

    public $args;

    public function tagName(): string
    {
        return 'Inject';
    }
}