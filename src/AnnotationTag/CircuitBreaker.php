<?php


namespace EasySwoole\HttpAnnotation\AnnotationTag;


use EasySwoole\Annotation\AbstractAnnotationTag;
/**
 * Class CircuitBreaker
 * @package EasySwoole\HttpAnnotation\AnnotationTag
 * @Annotation
 */
final class CircuitBreaker extends AbstractAnnotationTag
{
    /** @var float  */
    protected $timeout = 3.0;
    /** @var string */
    protected $failAction;
    public function tagName(): string
    {
        return 'CircuitBreaker';
    }

    public function assetValue(?string $raw)
    {
        // TODO: Implement assetValue() method.
    }
}