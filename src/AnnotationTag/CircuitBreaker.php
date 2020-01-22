<?php


namespace EasySwoole\HttpAnnotation\AnnotationTag;


use EasySwoole\Annotation\AbstractAnnotationTag;
use EasySwoole\Annotation\ValueParser;

/**
 * Class CircuitBreaker
 * @package EasySwoole\HttpAnnotation\AnnotationTag
 * @Annotation
 */
final class CircuitBreaker extends AbstractAnnotationTag
{
    /** @var float  */
    public $timeout = 3.0;
    /** @var string */
    public $failAction;
    public function tagName(): string
    {
        return 'CircuitBreaker';
    }

    public function assetValue(?string $raw)
    {
        $arr = ValueParser::parser($raw);
        if(!empty($arr['timeout'])){
            $this->timeout = $arr['timeout'];
        }
        if(!empty($arr['failAction'])){
            $this->failAction = $arr['failAction'];
        }
    }
}