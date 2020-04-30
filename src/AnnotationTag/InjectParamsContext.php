<?php


namespace EasySwoole\HttpAnnotation\AnnotationTag;

use EasySwoole\Annotation\AbstractAnnotationTag;
use EasySwoole\Annotation\ValueParser;

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
    public $filterNull = false;

    /**
     * @var bool
     */
    public $filterEmpty = false;

    public function tagName(): string
    {
        return 'InjectParamsContext';
    }

    public function assetValue(?string $raw)
    {
        $allParams = ValueParser::parser($raw);
        foreach ($allParams as $key => $value){
            $this->$key = $value;
        }
    }
}