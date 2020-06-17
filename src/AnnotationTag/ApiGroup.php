<?php


namespace EasySwoole\HttpAnnotation\AnnotationTag;


use EasySwoole\Annotation\AbstractAnnotationTag;

/**
 * Class ApiGroup
 * @package EasySwoole\HttpAnnotation\AnnotationTag
 * @Annotation
 */
class ApiGroup extends AbstractAnnotationTag
{
    public $groupName;

    public function tagName(): string
    {
        return 'ApiGroup';
    }
}