<?php


namespace EasySwoole\HttpAnnotation\AnnotationTag;


use EasySwoole\Annotation\AbstractAnnotationTag;

/**
 * Class Api
 * @package EasySwoole\HttpAnnotation\AnnotationTag
 * @Annotation
 */
class Api extends AbstractAnnotationTag
{
    /**
     * @var string
     */
    public $path;
    /**
     * @var string
     */
    public $name;
    /**
     * @var string
     */
    public $description;
    /**
     * @var bool
     */
    public $deprecated;
    /**
     * @var string
     */
    public $version = '1.0.0';


    /**
     * @var string
     */
    public $group;

    public function tagName(): string
    {
        return  'Api';
    }
}