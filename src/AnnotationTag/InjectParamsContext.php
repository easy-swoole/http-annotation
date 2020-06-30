<?php


namespace EasySwoole\HttpAnnotation\AnnotationTag;

use EasySwoole\Annotation\AbstractAnnotationTag;
/**
 * Class InjectParamsContext
 * @package EasySwoole\HttpAnnotation\AnnotationTag
 * @Annotation
 */
class InjectParamsContext extends AbstractAnnotationTag
{
    public $key = 'INJECT_PARAMS';
    /**
     * @var bool
     */
    public $filterNull = true;

    /**
     * @var bool
     */
    public $filterEmpty = false;

    public function tagName(): string
    {
        return 'InjectParamsContext';
    }
}