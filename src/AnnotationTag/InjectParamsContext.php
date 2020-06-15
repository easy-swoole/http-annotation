<?php


namespace EasySwoole\HttpAnnotation\AnnotationTag;

use EasySwoole\Annotation\AbstractAnnotationTag;
use EasySwoole\Annotation\ValueParser;
use EasySwoole\HttpAnnotation\Exception\Annotation\InvalidTag;

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
        if(empty($this->key)){
            throw new InvalidTag("InjectParamsContext key is required");
        }
    }
}